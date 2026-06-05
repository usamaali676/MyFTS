<?php

namespace App\Services;

class ZKTecoTCP
{
    private $socket;
    private $sessionId = 0;
    private $replyId   = 0;
    private $ip;
    private $port;
    private $commKey;  // 👈 communication password (default 0)

    const CMD_CONNECT       = 1000;
    const CMD_EXIT          = 1001;
    const CMD_ENABLEDEVICE  = 1002;
    const CMD_DISABLEDEVICE = 1003;
    const CMD_AUTH          = 1102;  // 👈 NEW
    const CMD_ACK_OK        = 2000;
    const CMD_ACK_UNAUTH    = 2005;  // 👈 NEW
    const CMD_USERTEMP_RRQ  = 9;
    const CMD_ATTLOG_RRQ    = 13;
    const CMD_FREE_DATA     = 0xFFFF;
    const CMD_DATA          = 0x14;
    const CMD_PREPARE_DATA  = 0x13;


    public function __construct($ip, $port = 4370, $commKey = 123456)
    {
        $this->ip      = $ip;
        $this->port    = $port;
        $this->commKey = $commKey;
    }

    // ─── Connection ──────────────────────────────────────────────

public function connect()
{
    $this->socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 10);

    if (!$this->socket) {
        return false;
    }

    stream_set_timeout($this->socket, 10);

    $response = $this->sendCommand(self::CMD_CONNECT);

    if ($response === false || strlen($response) < 8) {
        return false;
    }

    $header          = unpack('vcommand/vchecksum/vsession_id/vreply_id', substr($response, 0, 8));
    $this->sessionId = $header['session_id'];
    $responseCmd     = $header['command'];

    if ($responseCmd === self::CMD_ACK_OK) {
        return true; // No auth needed
    }

    if ($responseCmd === self::CMD_ACK_UNAUTH) {
        // If commKey is 0, send empty/zero auth payload
        if ($this->commKey === 0) {
            return $this->authenticateZero();
        }
        return $this->authenticate();
    }

    return false;
}

// ── Zero/blank password auth ──────────────────────────────────────
private function authenticateZero()
{
    // Some devices just need CMD_AUTH with a zero payload
    $authData = pack('V', 0);
    $response = $this->sendCommand(self::CMD_AUTH, $authData);

    if ($response === false || strlen($response) < 2) {
        return false;
    }

    $cmd = unpack('v', substr($response, 0, 2))[1];
    return $cmd === self::CMD_ACK_OK;
}

// private function makeCommKey($key, $sessionId)
// {
//     // Step 1: Sum all digit values of the key string
//     $k      = 0;
//     $keyStr = (string) $key;

//     for ($i = 0; $i < strlen($keyStr); $i++) {
//         $c = $keyStr[$i];
//         $k += ($c >= '0' && $c <= '9') ? (int) $c : ord($c);
//     }

//     // Step 2: Add session ID
//     $k = ($k + $sessionId) & 0xFFFFFFFF;

//     // Step 3: Rotate left 16 bits
//     $k = (($k << 16) | ($k >> 16)) & 0xFFFFFFFF;

//     // Step 4: Multiply — 0x4E534A4E = 1314212430
//     $k = (int) bcmod(bcmul((string) $k, '1314212430'), '4294967296');
//     $k = $k & 0xFFFFFFFF;

//     // Step 5: XOR — 0xA182BE5F = 2710208095
//     $k = ($k ^ 0xA182BE5F) & 0xFFFFFFFF;

//     // Step 6: Add — 0x06DC8B4A = 114983754
//     $k = ($k + 0x06DC8B4A) & 0xFFFFFFFF;

//     // Step 7: Rotate left 16 bits
//     $k = (($k << 16) | ($k >> 16)) & 0xFFFFFFFF;

//     // Step 8: XOR with session mask
//     $sessionMask = ($sessionId | ($sessionId << 16)) & 0xFFFFFFFF;
//     $k           = ($k ^ $sessionMask) & 0xFFFFFFFF;

//     return $k;
// }

private function authenticate()
{
    $authKey = $this->makeCommKey($this->commKey, $this->sessionId);

    echo "New auth key: $authKey (hex: " . dechex($authKey) . ")<br>";

    // Some firmware expects 8 bytes, some 4 — try 8 bytes
    $authData = pack('VV', $authKey, 0);

    $response = $this->sendCommand(self::CMD_AUTH, $authData);

    if ($response === false || strlen($response) < 2) {
        echo "❌ No response to CMD_AUTH<br>";
        return false;
    }

    $cmd = unpack('v', substr($response, 0, 2))[1];
    echo "Auth cmd response: $cmd<br>";

    if ($cmd === self::CMD_ACK_OK) {
        return true;
    }

    // ── Fallback: try 4-byte payload ──────────────────────────────
    echo "Trying 4-byte fallback...<br>";
    $authData = pack('V', $authKey);
    $response = $this->sendCommand(self::CMD_AUTH, $authData);
    $cmd      = unpack('v', substr($response, 0, 2))[1];
    echo "Fallback auth cmd: $cmd<br>";

    return $cmd === self::CMD_ACK_OK;
}

private function makeCommKey($key, $sessionId, $ticks = 50)
{
    $k = 0;

    // ── Loop 32 times using key + sessionId bits ───────────────
    for ($i = 0; $i < 32; $i++) {
        // Shift right, conditionally set MSB based on key bit
        if ($key & 1) {
            $k = (($k >> 1) & 0x7FFFFFFF) | 0x80000000;
        } else {
            $k = ($k >> 1) & 0x7FFFFFFF;
        }

        // XOR with constant if session bit is set
        if ($sessionId & 1) {
            $k ^= 0x4F534B10;
        }

        $k         = $k & 0xFFFFFFFF;
        $key       = ($key >> 1) & 0x7FFFFFFF;
        $sessionId = ($sessionId >> 1) & 0x7FFFFFFF;
    }

    // ── Rotate LEFT by ticks (pyzk: k << 1 | k >> 31) ────────
    for ($i = 0; $i < $ticks; $i++) {
        $k = (($k << 1) | ($k >> 31)) & 0xFFFFFFFF; // ← LEFT rotation
    }

    return $k;
}
private function byteSwap32(int $k): int
{
    $k = $k & 0xFFFFFFFF;

    return (($k & 0xFF000000) >> 24) |  // byte 3 → byte 0
           (($k & 0x00FF0000) >> 8)  |  // byte 2 → byte 1
           (($k & 0x0000FF00) << 8)  |  // byte 1 → byte 2
           (($k & 0x000000FF) << 24);   // byte 0 → byte 3
}

// Clamp to signed 32-bit integer
private function s32(int $val): int
{
    $val &= 0xFFFFFFFF;
    return $val >= 0x80000000 ? $val - 0x100000000 : $val;
}

// Clamp to unsigned 32-bit integer
private function u32(int $val): int
{
    return $val & 0xFFFFFFFF;
}

// private function authenticate()
// {
//     $authKey  = $this->makeCommKey($this->commKey, $this->sessionId);

//     // Pack as unsigned 32-bit little-endian, pad to 32 bytes
//     $authData = str_pad(pack('V', $authKey), 32, "\x00");

//     $response = $this->sendCommand(self::CMD_AUTH, $authData);

//     if ($response === false || strlen($response) < 2) {
//         return false;
//     }

//     $cmd = unpack('v', substr($response, 0, 2))[1];

//     return $cmd === self::CMD_ACK_OK;
// }

    public function disconnect()
    {
        $this->sendCommand(self::CMD_EXIT);
        if ($this->socket) {
            fclose($this->socket);
        }
    }

    public function disableDevice()
    {
        return $this->sendCommand(self::CMD_DISABLEDEVICE);
    }

    public function enableDevice()
    {
        return $this->sendCommand(self::CMD_ENABLEDEVICE);
    }

    // ─── Fetch Users ─────────────────────────────────────────────

    public function getUsers()
    {
        $raw = $this->readLargeData(self::CMD_USERTEMP_RRQ);

        if (!$raw) {
            return [];
        }

        $users  = [];
        $offset = 0;

        while ($offset < strlen($raw)) {
            if (strlen($raw) - $offset < 72) break;

            $record = unpack(
                'vuid/Cprivilege/a8password/a24name/a5card/Cgroup/Ctimezone/vtimezone2',
                substr($raw, $offset, 72)
            );

            $users[] = [
                'uid'       => $record['uid'],
                'privilege' => $record['privilege'],
                'password'  => rtrim($record['password'], "\x00"),
                'name'      => rtrim($record['name'],     "\x00"),
                'card'      => rtrim($record['card'],     "\x00"),
            ];

            $offset += 72;
        }

        return $users;
    }

    // ─── Fetch Attendance ─────────────────────────────────────────

    public function getAttendance()
    {
        $raw = $this->readLargeData(self::CMD_ATTLOG_RRQ);

        if (!$raw) {
            return [];
        }

        $attendance = [];
        $offset     = 0;
        $recordSize = 40;

        while ($offset + $recordSize <= strlen($raw)) {
            $record = substr($raw, $offset, $recordSize);

            $uid       = unpack('v', substr($record, 0, 2))[1];
            $timestamp = $this->parseTime(substr($record, 2, 4));
            $status    = ord($record[6]);
            $punch     = ord($record[7]);

            if ($uid > 0) {
                $attendance[] = [
                    'uid'       => $uid,
                    'timestamp' => $timestamp,
                    'status'    => $status,
                    'punch'     => $punch,
                ];
            }

            $offset += $recordSize;
        }

        return $attendance;
    }

    // ─── Protocol Internals ───────────────────────────────────────

    private function sendCommand($command, $data = '')
    {
        $buf      = pack('vvvv', $command, 0, $this->sessionId, $this->replyId) . $data;
        $checksum = $this->calcChecksum($buf);
        $buf      = pack('vvvv', $command, $checksum, $this->sessionId, $this->replyId) . $data;

        $this->replyId++;

        $tcpHeader = "\x50\x50\x82\x7d" . pack('V', strlen($buf));

        $result = @fwrite($this->socket, $tcpHeader . $buf);

        if ($result === false) {
            return false;
        }

        return $this->readPacket();
    }

    private function readLargeData($command)
    {
        $response = $this->sendCommand($command);

        if ($response === false) {
            return false;
        }

        $header = unpack('vcommand', substr($response, 0, 2));

        if ($header['command'] === self::CMD_PREPARE_DATA) {
            $size    = unpack('V', substr($response, 8, 4))[1];
            $payload = '';

            while (strlen($payload) < $size) {
                $packet = $this->readPacket();
                if ($packet === false) break;

                $cmd = unpack('v', substr($packet, 0, 2))[1];
                if ($cmd === self::CMD_DATA) {
                    $payload .= substr($packet, 8);
                }
            }

            $this->sendCommand(self::CMD_FREE_DATA);
            return $payload;
        }

        return substr($response, 8);
    }

    private function readPacket()
    {
        $header = $this->readBytes(8);

        if ($header === false || strlen($header) < 8) {
            return false;
        }

        $magic  = substr($header, 0, 4);
        $length = unpack('V', substr($header, 4, 4))[1];

        if ($magic !== "\x50\x50\x82\x7d" || $length === 0) {
            return false;
        }

        return $this->readBytes($length);
    }

    private function readBytes($length)
    {
        $buffer = '';

        while (strlen($buffer) < $length) {
            $chunk = @fread($this->socket, $length - strlen($buffer));
            if ($chunk === false || strlen($chunk) === 0) break;
            $buffer .= $chunk;
        }

        return $buffer ?: false;
    }

    private function calcChecksum($buf)
    {
        $sum    = 0;
        $padded = str_pad($buf, ceil(strlen($buf) / 2) * 2, "\x00");

        for ($i = 0; $i < strlen($padded); $i += 2) {
            $sum += unpack('v', $padded[$i] . $padded[$i + 1])[1];
        }

        while ($sum >> 16) {
            $sum = ($sum & 0xFFFF) + ($sum >> 16);
        }

        return ~$sum & 0xFFFF;
    }

    private function parseTime($bytes)
    {
        $t      = unpack('V', $bytes)[1];
        $second = $t % 60;        $t = intdiv($t, 60);
        $minute = $t % 60;        $t = intdiv($t, 60);
        $hour   = $t % 24;        $t = intdiv($t, 24);
        $day    = $t % 31 + 1;    $t = intdiv($t, 31);
        $month  = $t % 12 + 1;    $t = intdiv($t, 12);
        $year   = $t + 2000;

        return sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);
    }
    public function debugAuth()
    {
        $this->socket = @fsockopen($this->ip, $this->port, $errno, $errstr, 10);
        stream_set_timeout($this->socket, 10);

        // ── Step 1: CMD_CONNECT ───────────────────────────────────
        $response        = $this->sendCommand(self::CMD_CONNECT);
        $header          = unpack('vcommand/vchecksum/vsession_id/vreply_id', substr($response, 0, 8));
        $this->sessionId = $header['session_id'];

        echo "Session ID: {$this->sessionId}<br>";
        echo "Connect cmd: {$header['command']}<br><br>";

        // ── Step 2: Compute key ───────────────────────────────────
        $authKey  = $this->makeCommKey($this->commKey, $this->sessionId);
        $authData = pack('V', $authKey);

        echo "Comm key input: {$this->commKey}<br>";
        echo "Computed auth key: {$authKey} (hex: " . dechex($authKey) . ")<br>";
        echo "Auth data bytes: " . bin2hex($authData) . "<br><br>";

        // ── Step 3: Build CMD_AUTH packet manually ─────────────────
        $command  = self::CMD_AUTH; // 1102
        $buf      = pack('vvvv', $command, 0, $this->sessionId, $this->replyId) . $authData;

        // Checksum
        $sum    = 0;
        $padded = str_pad($buf, ceil(strlen($buf) / 2) * 2, "\x00");
        for ($i = 0; $i < strlen($padded); $i += 2) {
            $sum += unpack('v', $padded[$i] . $padded[$i + 1])[1];
        }
        while ($sum >> 16) $sum = ($sum & 0xFFFF) + ($sum >> 16);
        $checksum = ~$sum & 0xFFFF;

        $buf       = pack('vvvv', $command, $checksum, $this->sessionId, $this->replyId) . $authData;
        $tcpHeader = "\x50\x50\x82\x7d" . pack('V', strlen($buf));
        $fullPacket = $tcpHeader . $buf;

        echo "Full CMD_AUTH packet hex we send:<br>";
        echo "<b>" . bin2hex($fullPacket) . "</b><br>";
        echo "Packet length: " . strlen($fullPacket) . " bytes<br><br>";

        // ── Step 4: Send and read response ───────────────────────
        $response = $this->sendCommand(self::CMD_AUTH, $authData);
        $cmd      = unpack('v', substr($response, 0, 2))[1];

        echo "Auth response cmd: {$cmd} (2000=OK, 2005=fail)<br>";
        echo "Auth response hex: " . bin2hex($response) . "<br>";

        fclose($this->socket);
        dd('debug complete');
    }
}
