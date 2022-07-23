<?php

declare(strict_types=1);

namespace App\Utils;
 
use UnexpectedValueException;
use InvalidArgumentException;

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\Plain;

use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\IssuedBy;
use Lcobucci\JWT\Validation\Constraint\IdentifiedBy;
use Lcobucci\JWT\Validation\Constraint\ValidAt;

use Lcobucci\Clock\SystemClock;
use DateTimeImmutable;
use DateTimeZone;

final class Jwt
{
    /** @var string The issuer name */
    private string $issuer;
 
    /** @var int Max lifetime in seconds */
    private int $lifetime;

    /** @var Configuration config */
    private Configuration $config;
 
    /**
     * The constructor.
     *
     * @param string $issuer The issuer name
     * @param int $lifetime The max lifetime
     * @param string $privateKey The private key as string
     * @param string $publicKey The public key as string
     */
    public function __construct(
        string $issuer,
        int $lifetime,
        string $privateKey,
        string $publicKey
    ) {
        $this->issuer = $issuer;
        $this->lifetime = $lifetime;
        $this->signer = new Signer\Rsa\Sha256();
        $this->publicKey = $publicKey;
        $this->config = Configuration::forAsymmetricSigner(
          // You may use RSA or ECDSA and all their variations (256, 384, and 512) and EdDSA over Curve25519
          $this->signer,
          InMemory::plainText($privateKey), 
          InMemory::plainText($publicKey)
      );
    }
 
    /**
     * Get JWT max lifetime.
     *
     * @return int The lifetime in seconds
     */
    public function getLifetime(): int
    {
        return $this->lifetime;
    }
 
    /**
     * Create JSON web token.
     *
     * @param string $uid The user id
     *
     * @throws UnexpectedValueException
     *
     * @return string The JWT
     */
    public function createJwt(array $info): string
    {
      $issuedAt = new DateTimeImmutable();
      // print_r($this->v5_UUID("", 'JWT_TOKEN'));exit;
      return ($this->config->builder()
          ->issuedBy($this->issuer)
          ->permittedFor($this->issuer)
          ->identifiedBy($this->v5_UUID("0x752222", "JWT_TOKEN"), true)
          // Configures the time that the token was issue (iat claim)
          ->issuedAt($issuedAt)
          // Configures the time that the token can be used (nbf claim)
          ->canOnlyBeUsedAfter($issuedAt)
          // Configures the expiration time of the token (exp claim)
          ->expiresAt($issuedAt->modify("+{$this->lifetime} seconds"))
          // Configures a new claim, called "uid"
          ->withClaim("info", $info)
          // // Configures a new header, called "foo"
          // ->withHeader("foo", "bar")
          // Builds a new token
          ->getToken($this->config->signer(), $this->config->signingKey())
        )->toString();
    }
 
    /**
     * Parse token.
     *
     * @param string $token The JWT
     *
     * @throws InvalidArgumentException
     *
     * @return Token The parsed token
     */
    public function createParsedToken(string $token): Plain
    {
        return $this->config->parser()->parse($token);
    }
 
    /**
     * Validate the access token.
     *
     * @param string $accessToken The JWT
     *
     * @return bool The status
     */
    public function validateToken(string $accessToken): bool
    {
      $token = $this->createParsedToken($accessToken);
 
      $this->config->setValidationConstraints(new SignedWith($this->config->signer(), $this->config->verificationKey()));
      $this->config->setValidationConstraints(new IssuedBy($token->claims()->get("iss")));
      $this->config->setValidationConstraints(new IdentifiedBy($token->claims()->get("jti")));
      $this->config->setValidationConstraints(new ValidAt(new SystemClock(new DateTimeZone("Asia/Taipei"))));

      $constraints = $this->config->validationConstraints();
      if (!$this->config->validator()->validate($token, ...$constraints)) {
        return false;
      }
 
      return true;
    }

    public function v5_UUID(string $name_space, string $string): string {
      $n_hex = preg_replace('/[^0-9A-Fa-f\-\(\)]/', '', $name_space); // Getting hexadecimal components of namespace
      $binray_str = ''; // Binary value string
      //Namespace UUID to bits conversion
      for($i = 0; $i < strlen($n_hex); $i+=2) {
        if(!isset($n_hex[$i+1])) {
          $binray_str .= chr(hexdec($n_hex[$i]));
          break;
        }
        $binray_str .= chr(hexdec($n_hex[$i].$n_hex[$i+1]));
      }
      //hash value
      $hashing = sha1($binray_str . $string);
  
      return sprintf('%08s-%04s-%04x-%04x-%12s',
        // 32 bits for the time_low
        substr($hashing, 0, 8),
        // 16 bits for the time_mid
        substr($hashing, 8, 4),
        // 16 bits for the time_hi,
        (hexdec(substr($hashing, 12, 4)) & 0x0fff) | 0x5000,
        // 8 bits and 16 bits for the clk_seq_hi_res,
        // 8 bits for the clk_seq_low,
        (hexdec(substr($hashing, 16, 4)) & 0x3fff) | 0x8000,
        // 48 bits for the node
        substr($hashing, 20, 12)
      );
    }
}