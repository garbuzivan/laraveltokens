<?php

namespace Garbuzivan\Laraveltokens;

use Exception;

class Coder
{
    /**
     * @param array  $payload
     * @param string $key
     * @param array  $head [exp - expiration]
     *
     * @return string
     * @throws Exception
     */
    public function encode(array $payload, string $key, array $head = []): string
    {
        $header = ['typ' => 'JWT', 'alg' => 'HS256', 'unixtime' => time()];
        $header = \array_merge($head, $header);
        $segments = [];
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
        $signing_input = \implode('.', $segments);
        $signature = $this->sign($signing_input, $key);
        $segments[] = $this->urlsafeB64Encode($signature);
        return \implode('.', $segments);
    }

    /**
     * @param string $token
     * @param string $key
     *
     * @return array
     * @throws Exception
     */
    public function decode(string $token, string $key): array
    {
        $data = [];
        $tks = \explode('.', $token);
        if (\count($tks) != 3) {
            return $data;
        }
        [$headb64, $bodyb64, $cryptob64] = $tks;
        $header = $this->jsonDecode($this->urlsafeB64Decode($headb64));
        $payload = $this->jsonDecode($this->urlsafeB64Decode($bodyb64));
        $sig = $this->urlsafeB64Decode($cryptob64);
        $segments = [];
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($header));
        $segments[] = $this->urlsafeB64Encode($this->jsonEncode($payload));
        $signing_input = \implode('.', $segments);
        if ($this->sign($signing_input, $key) == $sig) {
            return \array_merge($header, $payload);
        }
        return $data;
    }

    /**
     * Decode a JSON string into a PHP object.
     *
     * @param string $input JSON string
     *
     * @return array Object representation of JSON string
     *
     * @throws Exception Provided string was invalid JSON
     */
    public function jsonDecode(string $input): array
    {
        $obj = \json_decode($input, true);
        if (is_null($obj)) {
            throw new Exception('Null result with non-null input');
        }
        return $obj;
    }

    /**
     * Encode a PHP object into a JSON string.
     *
     * @param array $input A PHP object or array
     *
     * @return string JSON representation of the PHP object or array
     *
     * @throws Exception Provided object could not be encoded to valid JSON
     */
    public function jsonEncode(array $input): string
    {
        $json = \json_encode($input);
        if ($errno = \json_last_error()) {
            $this->handleJsonError($errno);
        }
        return $json;
    }

    /**
     * Helper method to create a JSON error.
     *
     * @param int $errno An error number from json_last_error()
     *
     * @return void
     * @throws Exception
     */
    private function handleJsonError(int $errno)
    {
        $messages = [
            JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
            JSON_ERROR_STATE_MISMATCH => 'Invalid or malformed JSON',
            JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
            JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
            JSON_ERROR_UTF8           => 'Malformed UTF-8 characters' //PHP >= 5.3.3
        ];
        throw new Exception($messages[$errno] ?? 'Unknown JSON error: ' . $errno);
    }

    /**
     * @param string $msg
     * @param string $key
     *
     * @return false|string
     */
    public function sign(string $msg, string $key)
    {
        return \hash_hmac('SHA256', $msg, $key, true);
    }

    /**
     * Decode a string with URL-safe Base64.
     *
     * @param string $input A Base64 encoded string
     *
     * @return string A decoded string
     */
    public function urlsafeB64Decode($input)
    {
        $remainder = \strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= \str_repeat('=', $padlen);
        }
        return \base64_decode(\strtr($input, '-_', '+/'));
    }

    /**
     * Encode a string with URL-safe Base64.
     *
     * @param string $input The string you want encoded
     *
     * @return string The base64 encode of what you passed in
     */
    public function urlsafeB64Encode($input)
    {
        return \str_replace('=', '', \strtr(\base64_encode($input), '+/', '-_'));
    }
}
