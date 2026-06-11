<?php
declare(strict_types=1);

set_time_limit(0);
error_reporting(E_ALL);
ini_set('display_errors', '1');

$config2 = __DIR__ . '/../config/config2.php';
$config1 = __DIR__ . '/../config/config.php';
if (is_file($config2)) {
  require_once $config2;
} elseif (is_file($config1)) {
  require_once $config1;
}

$sessionName = defined('POS2_SESSION_NAME') ? (string)POS2_SESSION_NAME : 'POS2SESSID';

$host = '0.0.0.0';
$port = 8081;

$server = stream_socket_server("tcp://{$host}:{$port}", $errno, $errstr);
if ($server === false) {
  fwrite(STDERR, "No se pudo iniciar WS: {$errstr} ({$errno})\n");
  exit(1);
}
stream_set_blocking($server, false);

function chat_pdo(): ?PDO
{
  if (isset($GLOBALS['db']) && $GLOBALS['db'] instanceof PDO) return $GLOBALS['db'];
  if (defined('BD_HOST') && defined('BD_NAME') && defined('BD_USER') && defined('BD_PASSWORD')) {
    $charset = defined('BD_CHARSET') ? (string)BD_CHARSET : 'utf8mb4';
    return new PDO(
      "mysql:host=" . BD_HOST . ";dbname=" . BD_NAME . ";charset=" . $charset,
      BD_USER,
      BD_PASSWORD,
      [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
      ]
    );
  }
  return null;
}

function chat_ensure_tables(PDO $pdo): void
{
  $tableExists = static function (string $name) use ($pdo): bool {
    $st = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = ? LIMIT 1");
    $st->execute([$name]);
    return (bool)$st->fetchColumn();
  };

  $fkExists = static function (string $table, string $constraint) use ($pdo): bool {
    $st = $pdo->prepare("SELECT 1 FROM information_schema.table_constraints WHERE table_schema = DATABASE() AND table_name = ? AND constraint_name = ? AND constraint_type = 'FOREIGN KEY' LIMIT 1");
    $st->execute([$table, $constraint]);
    return (bool)$st->fetchColumn();
  };

  $hasUsers = $tableExists('tbl_usuarios');

  $pdo->exec(
    "CREATE TABLE IF NOT EXISTS chat_rooms (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      scope_type ENUM('GLOBAL','EMPRESA','CINE','DM') NOT NULL DEFAULT 'GLOBAL',
      scope_id INT NULL,
      nombre VARCHAR(120) NOT NULL,
      activo TINYINT(1) NOT NULL DEFAULT 1,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      KEY idx_scope (scope_type, scope_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  );

  try {
    $st = $pdo->prepare("SELECT column_type FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'chat_rooms' AND column_name = 'scope_type' LIMIT 1");
    $st->execute();
    $ct = (string)($st->fetchColumn() ?: '');
    if ($ct !== '' && stripos($ct, 'DM') === false) {
      $pdo->exec("ALTER TABLE chat_rooms MODIFY scope_type ENUM('GLOBAL','EMPRESA','CINE','DM') NOT NULL DEFAULT 'GLOBAL'");
    }
  } catch (Throwable $e) {
  }

  $pdo->exec(
    "CREATE TABLE IF NOT EXISTS chat_messages (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      room_id BIGINT UNSIGNED NOT NULL,
      user_id INT NULL,
      user_name VARCHAR(120) NOT NULL,
      mensaje TEXT NOT NULL,
      client_msg_id CHAR(36) NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      KEY idx_room_time (room_id, created_at),
      KEY idx_user_time (user_id, created_at),
      CONSTRAINT fk_chat_messages_room
        FOREIGN KEY (room_id) REFERENCES chat_rooms(id)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  );

  $pdo->exec(
    "CREATE TABLE IF NOT EXISTS chat_connections (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      user_id INT NULL,
      user_name VARCHAR(120) NOT NULL,
      room_id BIGINT UNSIGNED NOT NULL,
      cine_id INT NULL,
      ws_session_id CHAR(36) NULL,
      last_seen TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      connected TINYINT(1) NOT NULL DEFAULT 1,
      PRIMARY KEY (id),
      KEY idx_room_connected (room_id, connected),
      KEY idx_user_connected (user_id, connected),
      CONSTRAINT fk_chat_connections_room
        FOREIGN KEY (room_id) REFERENCES chat_rooms(id)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  );

  $pdo->exec(
    "CREATE TABLE IF NOT EXISTS chat_dm_rooms (
      id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
      room_id BIGINT UNSIGNED NOT NULL,
      user_low INT NOT NULL,
      user_high INT NOT NULL,
      created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (id),
      UNIQUE KEY uniq_users (user_low, user_high),
      KEY idx_room (room_id),
      CONSTRAINT fk_chat_dm_room
        FOREIGN KEY (room_id) REFERENCES chat_rooms(id)
        ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
  );

  if ($hasUsers) {
    if (!$fkExists('chat_messages', 'fk_chat_messages_user')) {
      try {
        $pdo->exec(
          "ALTER TABLE chat_messages
           ADD CONSTRAINT fk_chat_messages_user
             FOREIGN KEY (user_id) REFERENCES tbl_usuarios(id)
             ON DELETE SET NULL ON UPDATE CASCADE"
        );
      } catch (Throwable $e) {
      }
    }
    if (!$fkExists('chat_connections', 'fk_chat_connections_user')) {
      try {
        $pdo->exec(
          "ALTER TABLE chat_connections
           ADD CONSTRAINT fk_chat_connections_user
             FOREIGN KEY (user_id) REFERENCES tbl_usuarios(id)
             ON DELETE SET NULL ON UPDATE CASCADE"
        );
      } catch (Throwable $e) {
      }
    }
  }

  $stmt = $pdo->prepare("SELECT id FROM chat_rooms WHERE scope_type = 'GLOBAL' AND scope_id IS NULL LIMIT 1");
  $stmt->execute();
  $id = (int)($stmt->fetchColumn() ?: 0);
  if ($id <= 0) {
    $ins = $pdo->prepare("INSERT INTO chat_rooms (scope_type, scope_id, nombre, activo) VALUES ('GLOBAL', NULL, 'Chat global', 1)");
    $ins->execute();
  }
}

function chat_set_user_chat(PDO $pdo, int $userId, int $chat): void
{
  if ($userId <= 0) return;
  static $hasChatCol = null;
  if ($hasChatCol === null) {
    try {
      $st = $pdo->prepare("SELECT 1 FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = 'tbl_usuarios' LIMIT 1");
      $st->execute();
      if (!$st->fetchColumn()) {
        $hasChatCol = false;
      } else {
        $st2 = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'tbl_usuarios' AND column_name = 'chat' LIMIT 1");
        $st2->execute();
        $hasChatCol = (bool)$st2->fetchColumn();
      }
    } catch (Throwable $e) {
      $hasChatCol = false;
    }
  }
  if (!$hasChatCol) return;
  try {
    $st = $pdo->prepare("UPDATE tbl_usuarios SET chat = ? WHERE id = ? LIMIT 1");
    $st->execute([$chat ? 1 : 0, $userId]);
  } catch (Throwable $e) {
  }
}

function ws_accept_key(string $key): string
{
  return base64_encode(sha1($key . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11', true));
}

function ws_send($client, string $payload, int $opcode = 0x1): void
{
  $len = strlen($payload);
  $b1 = 0x80 | ($opcode & 0x0f);
  if ($len <= 125) {
    $header = pack('CC', $b1, $len);
  } elseif ($len <= 65535) {
    $header = pack('CCn', $b1, 126, $len);
  } else {
    $header = pack('CCNN', $b1, 127, 0, $len);
  }
  @fwrite($client, $header . $payload);
}

function ws_read_frame($client): ?array
{
  $hdr = @fread($client, 2);
  if ($hdr === false || $hdr === '') return null;
  if (strlen($hdr) < 2) return null;

  [$b1, $b2] = array_values(unpack('C2', $hdr));
  $fin = (bool)($b1 & 0x80);
  $opcode = $b1 & 0x0f;
  $masked = (bool)($b2 & 0x80);
  $len = $b2 & 0x7f;

  if ($len === 126) {
    $ext = @fread($client, 2);
    if ($ext === false || strlen($ext) < 2) return null;
    $len = unpack('n', $ext)[1];
  } elseif ($len === 127) {
    $ext = @fread($client, 8);
    if ($ext === false || strlen($ext) < 8) return null;
    $u = unpack('N2', $ext);
    $len = ($u[1] << 32) | $u[2];
  }

  $mask = '';
  if ($masked) {
    $mask = @fread($client, 4);
    if ($mask === false || strlen($mask) < 4) return null;
  }

  $data = '';
  $remaining = $len;
  while ($remaining > 0) {
    $chunk = @fread($client, min(8192, $remaining));
    if ($chunk === false || $chunk === '') break;
    $data .= $chunk;
    $remaining -= strlen($chunk);
  }

  if (strlen($data) !== $len) return null;

  if ($masked) {
    $unmasked = '';
    for ($i = 0; $i < $len; $i++) {
      $unmasked .= $data[$i] ^ $mask[$i % 4];
    }
    $data = $unmasked;
  }

  return [
    'fin' => $fin,
    'opcode' => $opcode,
    'payload' => $data,
  ];
}

function ws_parse_headers(string $buf): array
{
  $lines = preg_split("/\r\n/", $buf) ?: [];
  $headers = [];
  foreach ($lines as $line) {
    if (strpos($line, ':') !== false) {
      [$k, $v] = explode(':', $line, 2);
      $headers[strtolower(trim($k))] = trim($v);
    }
  }
  return $headers;
}

function ws_handshake($client, string $buf): ?array
{
  $headers = ws_parse_headers($buf);
  $key = (string)($headers['sec-websocket-key'] ?? '');
  if ($key === '') return false;
  $accept = ws_accept_key($key);
  $out = "HTTP/1.1 101 Switching Protocols\r\n"
    . "Upgrade: websocket\r\n"
    . "Connection: Upgrade\r\n"
    . "Sec-WebSocket-Accept: {$accept}\r\n"
    . "\r\n";
  @fwrite($client, $out);
  return $headers;
}

function cookie_get(string $cookieHeader, string $name): string
{
  $parts = array_map('trim', explode(';', $cookieHeader));
  foreach ($parts as $p) {
    if ($p === '' || strpos($p, '=') === false) continue;
    [$k, $v] = explode('=', $p, 2);
    if (trim($k) === $name) return trim($v);
  }
  return '';
}

function read_session_data(string $sessionName, string $sessionId): array
{
  if ($sessionId === '') return [];
  if (session_status() === PHP_SESSION_ACTIVE) {
    session_write_close();
  }
  session_name($sessionName);
  session_id($sessionId);
  @session_start();
  $data = $_SESSION ?? [];
  session_write_close();
  return is_array($data) ? $data : [];
}

function uuid_v4(): string
{
  $d = random_bytes(16);
  $d[6] = chr((ord($d[6]) & 0x0f) | 0x40);
  $d[8] = chr((ord($d[8]) & 0x3f) | 0x80);
  $hex = bin2hex($d);
  return substr($hex, 0, 8) . '-' . substr($hex, 8, 4) . '-' . substr($hex, 12, 4) . '-' . substr($hex, 16, 4) . '-' . substr($hex, 20, 12);
}

$clients = [];
$handshakes = [];
$users = [];

function broadcast_room(array $clients, array $users, int $roomId, string $payload, $skip = null): void
{
  foreach ($clients as $id => $c) {
    if ($skip !== null && $c === $skip) continue;
    if (!isset($users[$id]) || (int)($users[$id]['roomId'] ?? 0) !== $roomId) continue;
    ws_send($c, $payload, 0x1);
  }
}

function broadcast_all(array $clients, string $payload, $skip = null): void
{
  foreach ($clients as $c) {
    if ($skip !== null && $c === $skip) continue;
    ws_send($c, $payload, 0x1);
  }
}

function chat_get_global_room_id(PDO $pdo): int
{
  $st = $pdo->prepare("SELECT id FROM chat_rooms WHERE scope_type = 'GLOBAL' AND scope_id IS NULL LIMIT 1");
  $st->execute();
  return (int)($st->fetchColumn() ?: 0);
}

function chat_get_cine_room_id(PDO $pdo, int $cineId): int
{
  if ($cineId <= 0) return 0;
  $st = $pdo->prepare("SELECT id FROM chat_rooms WHERE scope_type = 'CINE' AND scope_id = ? LIMIT 1");
  $st->execute([$cineId]);
  $id = (int)($st->fetchColumn() ?: 0);
  if ($id > 0) return $id;
  $ins = $pdo->prepare("INSERT INTO chat_rooms (scope_type, scope_id, nombre, activo) VALUES ('CINE', ?, ?, 1)");
  $ins->execute([$cineId, 'Chat Cine ' . $cineId]);
  return (int)$pdo->lastInsertId();
}

function chat_get_room(PDO $pdo, int $roomId): ?array
{
  if ($roomId <= 0) return null;
  $st = $pdo->prepare("SELECT id, scope_type, scope_id, nombre FROM chat_rooms WHERE id = ? LIMIT 1");
  $st->execute([$roomId]);
  $r = $st->fetch(PDO::FETCH_ASSOC) ?: null;
  return is_array($r) ? $r : null;
}

function chat_get_or_create_dm_room_id(PDO $pdo, int $u1, int $u2): int
{
  $a = min($u1, $u2);
  $b = max($u1, $u2);
  if ($a <= 0 || $b <= 0 || $a === $b) return 0;
  $st = $pdo->prepare("SELECT room_id FROM chat_dm_rooms WHERE user_low = ? AND user_high = ? LIMIT 1");
  $st->execute([$a, $b]);
  $rid = (int)($st->fetchColumn() ?: 0);
  if ($rid > 0) return $rid;
  $insRoom = $pdo->prepare("INSERT INTO chat_rooms (scope_type, scope_id, nombre, activo) VALUES ('DM', NULL, ?, 1)");
  $insRoom->execute(['DM ' . $a . '-' . $b]);
  $roomId = (int)$pdo->lastInsertId();
  $insMap = $pdo->prepare("INSERT INTO chat_dm_rooms (room_id, user_low, user_high) VALUES (?, ?, ?)");
  $insMap->execute([$roomId, $a, $b]);
  return $roomId;
}

function chat_get_dm_participants(PDO $pdo, int $roomId): array
{
  $st = $pdo->prepare("SELECT user_low, user_high FROM chat_dm_rooms WHERE room_id = ? LIMIT 1");
  $st->execute([$roomId]);
  $r = $st->fetch(PDO::FETCH_ASSOC) ?: null;
  if (!$r) return [0, 0];
  return [(int)($r['user_low'] ?? 0), (int)($r['user_high'] ?? 0)];
}

function chat_users_has_chat_col(PDO $pdo): bool
{
  static $has = null;
  if ($has !== null) return (bool)$has;
  try {
    $st = $pdo->prepare("SELECT 1 FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'tbl_usuarios' AND column_name = 'chat' LIMIT 1");
    $st->execute();
    $has = (bool)$st->fetchColumn();
    return (bool)$has;
  } catch (Throwable $e) {
    $has = false;
    return false;
  }
}

function chat_fetch_users(PDO $pdo): array
{
  $hasChat = chat_users_has_chat_col($pdo);
  $sql = "SELECT u.id, u.usuario, u.nombre, u.id_local, u.estado"
    . ($hasChat ? ", u.chat" : "")
    . ", COALESCE(cc.online, 0) AS online"
    . " FROM tbl_usuarios u"
    . " LEFT JOIN ("
    . "   SELECT user_id, 1 AS online"
    . "   FROM chat_connections"
    . "   WHERE connected = 1 AND user_id IS NOT NULL"
    . "   GROUP BY user_id"
    . " ) cc ON cc.user_id = u.id"
    . " WHERE u.estado = '1'"
    . " ORDER BY u.nombre ASC, u.usuario ASC";
  $st = $pdo->prepare($sql);
  $st->execute();
  $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  return array_map(static function ($r) use ($hasChat) {
    $online = ((int)($r['online'] ?? 0) === 1);
    return [
      'id' => (int)($r['id'] ?? 0),
      'usuario' => (string)($r['usuario'] ?? ''),
      'nombre' => (string)($r['nombre'] ?? ''),
      'id_local' => isset($r['id_local']) ? (int)$r['id_local'] : null,
      'chat' => $hasChat ? (int)($r['chat'] ?? 0) : 0,
      'online' => $online,
    ];
  }, $rows);
}

function chat_refresh_user_chat(PDO $pdo, int $userId): void
{
  if ($userId <= 0) return;
  if (!chat_users_has_chat_col($pdo)) return;
  try {
    $st = $pdo->prepare("SELECT 1 FROM chat_connections WHERE user_id = ? AND connected = 1 LIMIT 1");
    $st->execute([$userId]);
    $online = (bool)$st->fetchColumn();
    chat_set_user_chat($pdo, $userId, $online ? 1 : 0);
  } catch (Throwable $e) {
  }
}

function chat_send_users(PDO $pdo, $client): void
{
  $users = chat_fetch_users($pdo);
  ws_send($client, json_encode(['type' => 'users', 'users' => $users], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
}

function chat_broadcast_users(PDO $pdo, array $clients): void
{
  $users = chat_fetch_users($pdo);
  $payload = json_encode(['type' => 'users', 'users' => $users], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
  foreach ($clients as $c) {
    ws_send($c, $payload, 0x1);
  }
}

function chat_send_history(PDO $pdo, $client, int $roomId): void
{
  if ($roomId <= 0) return;
  $st = $pdo->prepare("SELECT user_name, mensaje, created_at FROM chat_messages WHERE room_id = ? ORDER BY created_at DESC, id DESC LIMIT 50");
  $st->execute([$roomId]);
  $rows = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  $rows = array_reverse($rows);
  foreach ($rows as $r) {
    $out = [
      'type' => 'msg',
      'roomId' => $roomId,
      'user' => (string)($r['user_name'] ?? 'Usuario'),
      'text' => (string)($r['mensaje'] ?? ''),
      'ts' => gmdate('c', strtotime((string)($r['created_at'] ?? 'now'))),
    ];
    ws_send($client, json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
  }
}

$pdo = null;
try {
  $pdo = chat_pdo();
  if ($pdo) {
    chat_ensure_tables($pdo);
    try { $pdo->exec("UPDATE chat_connections SET connected = 0"); } catch (Throwable $e) {}
  }
} catch (Throwable $e) {
  $pdo = null;
}

while (true) {
  $read = array_merge([$server], $clients);
  $write = null;
  $except = null;
  @stream_select($read, $write, $except, 1);

  foreach ($read as $sock) {
    if ($sock === $server) {
      $client = @stream_socket_accept($server, 0);
      if ($client) {
        stream_set_blocking($client, false);
        $id = (int)$client;
        $clients[$id] = $client;
        $handshakes[$id] = '';
        $users[$id] = [
          'user' => '',
          'userId' => 0,
          'cineId' => 0,
          'roomId' => 0,
          'wsSessionId' => uuid_v4(),
          'cookie' => '',
          'sessionId' => '',
        ];
      }
      continue;
    }

    $id = (int)$sock;
    if (!isset($clients[$id])) continue;

    if (!isset($handshakes[$id]) || $handshakes[$id] !== true) {
      $buf = @fread($sock, 2048);
      if ($buf === false || $buf === '') {
        @fclose($sock);
        unset($clients[$id], $handshakes[$id], $users[$id]);
        continue;
      }
      $handshakes[$id] = ($handshakes[$id] ?? '') . $buf;
      if (strpos((string)$handshakes[$id], "\r\n\r\n") !== false) {
        $headers = ws_handshake($sock, (string)$handshakes[$id]);
        if (!$headers) {
          @fclose($sock);
          unset($clients[$id], $handshakes[$id], $users[$id]);
          continue;
        }
        $handshakes[$id] = true;
        $cookie = (string)($headers['cookie'] ?? '');
        $users[$id]['cookie'] = $cookie;
        $sid = cookie_get($cookie, $sessionName);
        if ($sid === '') $sid = cookie_get($cookie, 'PHPSESSID');
        $users[$id]['sessionId'] = $sid;
        ws_send($sock, json_encode(['type' => 'sys', 'user' => 'Sistema', 'text' => 'Conectado al chat', 'ts' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      }
      continue;
    }

    $frame = ws_read_frame($sock);
    if ($frame === null) {
      @fclose($sock);
      $u = $users[$id]['user'] ?? '';
      $uid = (int)($users[$id]['userId'] ?? 0);
      $roomId = (int)($users[$id]['roomId'] ?? 0);
      $wsSessionId = (string)($users[$id]['wsSessionId'] ?? '');
      if ($pdo && $wsSessionId !== '') {
        try {
          $st = $pdo->prepare("UPDATE chat_connections SET connected = 0, last_seen = CURRENT_TIMESTAMP WHERE ws_session_id = ? LIMIT 1");
          $st->execute([$wsSessionId]);
        } catch (Throwable $e) {
        }
      }
      if ($pdo && $uid > 0) {
        chat_refresh_user_chat($pdo, $uid);
        chat_broadcast_users($pdo, $clients);
      }
      unset($clients[$id], $handshakes[$id], $users[$id]);
      if ($u !== '' && $roomId > 0) {
        broadcast_room($clients, $users, $roomId, json_encode(['type' => 'sys', 'roomId' => $roomId, 'user' => 'Sistema', 'text' => "{$u} salió", 'ts' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      }
      continue;
    }

    $opcode = (int)$frame['opcode'];
    if ($opcode === 0x8) {
      ws_send($sock, '', 0x8);
      @fclose($sock);
      $u = $users[$id]['user'] ?? '';
      $uid = (int)($users[$id]['userId'] ?? 0);
      $roomId = (int)($users[$id]['roomId'] ?? 0);
      $wsSessionId = (string)($users[$id]['wsSessionId'] ?? '');
      if ($pdo && $wsSessionId !== '') {
        try {
          $st = $pdo->prepare("UPDATE chat_connections SET connected = 0, last_seen = CURRENT_TIMESTAMP WHERE ws_session_id = ? LIMIT 1");
          $st->execute([$wsSessionId]);
        } catch (Throwable $e) {
        }
      }
      if ($pdo && $uid > 0) {
        chat_refresh_user_chat($pdo, $uid);
        chat_broadcast_users($pdo, $clients);
      }
      unset($clients[$id], $handshakes[$id], $users[$id]);
      if ($u !== '' && $roomId > 0) {
        broadcast_room($clients, $users, $roomId, json_encode(['type' => 'sys', 'roomId' => $roomId, 'user' => 'Sistema', 'text' => "{$u} salió", 'ts' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
      }
      continue;
    }
    if ($opcode === 0x9) {
      ws_send($sock, (string)$frame['payload'], 0xA);
      continue;
    }
    if ($opcode !== 0x1) continue;

    $payload = (string)$frame['payload'];
    $msg = json_decode($payload, true);
    if (!is_array($msg)) continue;

    $type = (string)($msg['type'] ?? '');
    if ($type === 'hello') {
      $user = trim((string)($msg['user'] ?? ''));
      $userId = (int)($msg['userId'] ?? 0);
      $cineId = (int)($msg['cineId'] ?? 0);

      $sid = (string)($users[$id]['sessionId'] ?? '');
      if ($sid !== '') {
        $sdata = read_session_data($sessionName, $sid);
        if (is_array($sdata)) {
          if ($userId <= 0 && isset($sdata['idusuario'])) $userId = (int)$sdata['idusuario'];
          if ($user === '' && isset($sdata['usuario_nombre']) && (string)$sdata['usuario_nombre'] !== '') $user = (string)$sdata['usuario_nombre'];
          if ($user === '' && isset($sdata['usuario']) && (string)$sdata['usuario'] !== '') $user = (string)$sdata['usuario'];
          if ($cineId <= 0 && isset($sdata['local_id'])) $cineId = (int)$sdata['local_id'];
        }
      }

      $globalRoomId = 1;
      $cineRoomId = 0;
      if ($pdo) {
        try {
          $globalRoomId = chat_get_global_room_id($pdo) ?: 1;
          $cineRoomId = $cineId > 0 ? chat_get_cine_room_id($pdo, $cineId) : 0;
        } catch (Throwable $e) {
          $globalRoomId = 1;
          $cineRoomId = 0;
        }
      }
      $roomId = $globalRoomId;

      $users[$id] = [
        'user' => $user,
        'userId' => $userId,
        'cineId' => $cineId,
        'roomId' => $roomId,
        'wsSessionId' => (string)($users[$id]['wsSessionId'] ?? uuid_v4()),
        'cookie' => (string)($users[$id]['cookie'] ?? ''),
        'sessionId' => (string)($users[$id]['sessionId'] ?? ''),
      ];
      if ($user !== '') {
        broadcast_room($clients, $users, $roomId, json_encode(['type' => 'sys', 'roomId' => $roomId, 'user' => 'Sistema', 'text' => "{$user} entró", 'ts' => gmdate('c')], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $sock);
      }

      ws_send($sock, json_encode(['type' => 'hello_ack', 'globalRoomId' => $globalRoomId, 'cineRoomId' => $cineRoomId], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

      if ($pdo) {
        try {
          $wsSessionId = (string)($users[$id]['wsSessionId'] ?? '');
          $st = $pdo->prepare("INSERT INTO chat_connections (user_id, user_name, room_id, cine_id, ws_session_id, last_seen, connected) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, 1)");
          $st->execute([$userId > 0 ? $userId : null, $user !== '' ? $user : 'Usuario', $roomId, $cineId > 0 ? $cineId : null, $wsSessionId !== '' ? $wsSessionId : null]);
        } catch (Throwable $e) {
        }
        if ($userId > 0) {
          chat_refresh_user_chat($pdo, $userId);
          chat_broadcast_users($pdo, $clients);
        }
        try { chat_send_history($pdo, $sock, $roomId); } catch (Throwable $e) {}
        try { chat_send_users($pdo, $sock); } catch (Throwable $e) {}
      }
      continue;
    }

    if ($type === 'presence') {
      $connected = (int)($msg['connected'] ?? 0) ? 1 : 0;
      $userId = (int)($msg['userId'] ?? 0);
      if ($userId <= 0) $userId = (int)($users[$id]['userId'] ?? 0);
      $wsSessionId = (string)($users[$id]['wsSessionId'] ?? '');
      if ($pdo && $wsSessionId !== '') {
        try {
          $st = $pdo->prepare("UPDATE chat_connections SET user_id = ?, connected = ?, last_seen = CURRENT_TIMESTAMP WHERE ws_session_id = ? LIMIT 1");
          $st->execute([$userId > 0 ? $userId : null, $connected ? 1 : 0, $wsSessionId]);
          if ((int)$st->rowCount() === 0) {
            $roomId = (int)($users[$id]['roomId'] ?? 1);
            $cineId = (int)($users[$id]['cineId'] ?? 0);
            $user = (string)($users[$id]['user'] ?? '');
            if ($user === '') $user = $userId > 0 ? ('Usuario ' . $userId) : 'Usuario';
            $ins = $pdo->prepare("INSERT INTO chat_connections (user_id, user_name, room_id, cine_id, ws_session_id, last_seen, connected) VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP, ?)");
            $ins->execute([$userId > 0 ? $userId : null, $user, $roomId > 0 ? $roomId : 1, $cineId > 0 ? $cineId : null, $wsSessionId, $connected ? 1 : 0]);
          }
        } catch (Throwable $e) {
        }
        if ($userId > 0) {
          chat_refresh_user_chat($pdo, $userId);
          chat_broadcast_users($pdo, $clients);
        }
      }
      continue;
    }

    if ($type === 'list_users') {
      if ($pdo) {
        try { chat_send_users($pdo, $sock); } catch (Throwable $e) {}
      }
      continue;
    }

    if ($type === 'history') {
      $rid = (int)($msg['roomId'] ?? 0);
      if ($pdo && $rid > 0) {
        try { chat_send_history($pdo, $sock, $rid); } catch (Throwable $e) {}
      }
      continue;
    }

    if ($type === 'dm_open') {
      $to = (int)($msg['toUserId'] ?? 0);
      $from = (int)($users[$id]['userId'] ?? 0);
      if ($pdo && $to > 0 && $from > 0 && $to !== $from) {
        try {
          $rid = chat_get_or_create_dm_room_id($pdo, $from, $to);
          if ($rid > 0) {
            $users[$id]['roomId'] = $rid;
            ws_send($sock, json_encode(['type' => 'dm_room', 'roomId' => $rid, 'toUserId' => $to], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
            chat_send_history($pdo, $sock, $rid);
          }
        } catch (Throwable $e) {
        }
      }
      continue;
    }

    if ($type === 'msg') {
      $user = $users[$id]['user'] ?? '';
      if ($user === '') $user = trim((string)($msg['user'] ?? ''));
      $text = trim((string)($msg['text'] ?? ''));
      if ($text === '') continue;
      $roomId = (int)($msg['roomId'] ?? 0);
      if ($roomId <= 0) $roomId = (int)($users[$id]['roomId'] ?? 1);
      if ($roomId <= 0) $roomId = 1;
      $userId = (int)($users[$id]['userId'] ?? 0);
      $cineId = (int)($users[$id]['cineId'] ?? 0);
      $out = [
        'type' => 'msg',
        'roomId' => $roomId,
        'user' => $user !== '' ? $user : 'Usuario',
        'text' => $text,
        'ts' => gmdate('c'),
      ];
      if ($pdo) {
        try {
          $st = $pdo->prepare("INSERT INTO chat_messages (room_id, user_id, user_name, mensaje, created_at) VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP)");
          $st->execute([$roomId, $userId > 0 ? $userId : null, $out['user'], $text]);
        } catch (Throwable $e) {
        }
        try {
          $wsSessionId = (string)($users[$id]['wsSessionId'] ?? '');
          if ($wsSessionId !== '') {
            $st = $pdo->prepare("UPDATE chat_connections SET last_seen = CURRENT_TIMESTAMP WHERE ws_session_id = ? LIMIT 1");
            $st->execute([$wsSessionId]);
          }
        } catch (Throwable $e) {
        }
      }
      $payloadOut = json_encode($out, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
      if ($pdo) {
        try {
          $room = chat_get_room($pdo, $roomId);
          $scope = $room ? (string)($room['scope_type'] ?? '') : '';
          if ($scope === 'DM') {
            [$a, $b] = chat_get_dm_participants($pdo, $roomId);
            foreach ($clients as $cid => $c) {
              $uid = (int)($users[$cid]['userId'] ?? 0);
              if ($uid === $a || $uid === $b) ws_send($c, $payloadOut);
            }
          } elseif ($scope === 'CINE') {
            $scopeCine = $room ? (int)($room['scope_id'] ?? 0) : 0;
            $targetCine = $scopeCine > 0 ? $scopeCine : $cineId;
            foreach ($clients as $cid => $c) {
              if ((int)($users[$cid]['cineId'] ?? 0) === $targetCine) ws_send($c, $payloadOut);
            }
          } else {
            foreach ($clients as $c) ws_send($c, $payloadOut);
          }
        } catch (Throwable $e) {
          foreach ($clients as $c) ws_send($c, $payloadOut);
        }
      } else {
        broadcast_room($clients, $users, $roomId, $payloadOut);
      }
      continue;
    }
  }
}

