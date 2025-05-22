<?php

declare(strict_types=1);

namespace Fabledsolutions\GithubSdk;


require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class Client
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client|null
     */
    private static $client = null;

    /**
     * @var array|null
     */
    private static $config = null;

    /**
     * @return \GuzzleHttp\Client
     * @throws \Exception Se houver erro na configuração ou inicialização.
     */
    public static function setup(): \GuzzleHttp\Client
    {
        if (self::$client !== null) {
            return self::$client;
        }

        try {
            self::$config = GITHUB_CONFIG_ENV;
            self::$client = new \GuzzleHttp\Client([
                'base_uri' => self::$config['GITHUB_API_URL'],
                'headers' => [
                    'Authorization' => 'token ' . self::$config['GITHUB_TOKEN'],
                    'Accept' => 'application/vnd.github.v3+json',
                ],
            ]);
            return self::$client;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            throw new \Exception("RequestException: " . $e->getMessage(), 0, $e);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            throw new \Exception("GuzzleException: " . $e->getMessage(), 0, $e);
        } catch (\Exception $e) {
            throw new \Exception("General Exception: " . $e->getMessage(), 0, $e);
        }
    }
}
