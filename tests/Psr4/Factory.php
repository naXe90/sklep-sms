<?php
namespace Tests\Psr4;

use App\Repositories\PricelistRepository;
use App\Repositories\ServerRepository;
use App\Repositories\ServerServiceRepository;
use App\Repositories\UserRepository;
use Faker\Factory as FakerFactory;
use Faker\Generator;

class Factory
{
    /** @var Generator */
    protected $faker;

    /** @var UserRepository */
    protected $userRepository;

    /** @var ServerRepository */
    protected $serverRepository;

    /** @var PricelistRepository */
    protected $pricelistRepository;

    /** @var ServerServiceRepository */
    protected $serverServiceRepository;

    public function __construct(
        UserRepository $userRepository,
        ServerRepository $serverRepository,
        PricelistRepository $pricelistRepository,
        ServerServiceRepository $serverServiceRepository
    ) {
        $this->userRepository = $userRepository;
        $this->serverRepository = $serverRepository;
        $this->pricelistRepository = $pricelistRepository;
        $this->serverServiceRepository = $serverServiceRepository;
        $this->faker = FakerFactory::create();
    }

    public function server(array $attributes = [])
    {
        $attributes = array_merge([
            'name' => $this->faker->sentence,
            'ip'   => $this->faker->ipv4,
            'port' => $this->faker->numberBetween(1000, 20000),
        ], $attributes);

        return $this->serverRepository->create($attributes['name'], $attributes['ip'], $attributes['port']);
    }

    public function serverService(array $attributes = [])
    {
        $attributes = array_merge([
            'service_id' => 'gosetti',
        ], $attributes);

        return $this->serverServiceRepository->create($attributes['server_id'], $attributes['service_id']);
    }

    public function pricelist(array $attributes = [])
    {
        $attributes = array_merge([
            'service_id' => 'gosetti',
            'amount'     => $this->faker->numberBetween(1, 100),
        ], $attributes);

        return $this->pricelistRepository->create(
            $attributes['service_id'],
            $attributes['tariff'],
            $attributes['amount'],
            $attributes['server_id']
        );
    }

    public function user(array $attributes = [])
    {
        $attributes = array_merge([
            'username' => $this->faker->userName,
            'password' => $this->faker->password,
            'email'    => $this->faker->email,
            'forename' => $this->faker->firstName,
            'surname'  => $this->faker->lastName,
            'ip'       => $this->faker->ipv4,
            'groups'   => '2',
        ], $attributes);

        return $this->userRepository->create(
            $attributes['username'],
            $attributes['password'],
            $attributes['email'],
            $attributes['forename'],
            $attributes['surname'],
            $attributes['ip'],
            $attributes['groups']
        );
    }
}
