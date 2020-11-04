<?php
declare(strict_types=1);
namespace Pila\Dashboard;

use Pila\Dashboard\ApiInterface;
use Phpfastcache\Helper\Psr16Adapter;
use Phpfastcache\Config\ConfigurationOption;

class WeatherApi implements ApiInterface
{
    public string $city;
    protected string $driver = "Files";
    protected string $apiUrl;
    private Psr16Adapter $cache;
    private string $key;
    private array $result;
    private array $weather;
    private array $location;

    /**
     * Constructor
     *
     * @param string $key API key.
     * @param string $city City weather data to fetch.
     */
    public function __construct(string $key, string $city)
    {
        $this->cache = new Psr16Adapter(
            $this->driver,
            new ConfigurationOption([ /** @phpstan-ignore-line False positive. */
                'path' => __DIR__ . '/../cache',
                'defaultTtl' => 3600,
            ])
        );
        $this->key = $key;
        $this->city = urlencode($city);
        $this->apiUrl =
            "https://api.weatherapi.com/v1/current.json?key={$this->key}&q={$this->city}&lang=cs";
        $this->result = json_decode($this->call(), true);
        $this->weather = $this->result['current'];
        $this->location = $this->result['location'];
    }

    /**
     * {@inheritDoc}
     */
    public function call(): string
    {
        if (!$this->cache->has("weather-api-call-{$this->city}-result")) {
            $data = file_get_contents($this->apiUrl);
            $this->cache->set("weather-api-call-{$this->city}-result", $data, 3600);
        } else {
            $data = $this->cache->get("weather-api-call-{$this->city}-result");
        }
        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function all(): array
    {
        return $this->result;
    }

    /**
     * Fetches a location info.
     */
    public function location(): array
    {
        return $this->location;
    }

    /**
     * Returns a city.
     */
    public function city(): string
    {
        return $this->location()['name'];
    }

    /**
     * Returns a region.
     */
    public function region(): string
    {
        return $this->location()['region'];
    }

    /**
     * Returns a country.
     */
    public function country(): string
    {
        return $this->location()['country'];
    }

    /**
     * Returns all weather info
     */
    public function weather(): array
    {
        return $this->weather;
    }

    /**
     * Returns current temperature.
     */
    public function temp(): float
    {
        return $this->weather['temp_c'];
    }

    /**
     * Returns condition.
     */
    public function condition(): string
    {
        return $this->weather['condition']['text'];
    }

    /**
     * Returns condition icon URL.
     */
    public function conditionIcon(): string
    {
        return $this->weather['condition']['icon'];
    }

    /**
     * Returns last update datetime.
     */
    public function lastUpdated(): string
    {
        return $this->weather['last_updated'];
    }
}
