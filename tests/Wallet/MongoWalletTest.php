<?php

declare(strict_types=1);

namespace Damax\ChargeableApi\Tests\Wallet;

use Damax\ChargeableApi\Credit;
use Damax\ChargeableApi\InsufficientFunds;
use Damax\ChargeableApi\Wallet\MongoWallet;
use MongoDB\Client;
use MongoDB\Collection;
use MongoDB\Model\BSONDocument;
use PHPUnit\Framework\TestCase;

class MongoWalletTest extends TestCase
{
    /**
     * @var Collection
     */
    private static $collection;

    /**
     * @var MongoWallet
     */
    private $wallet;

    public static function setUpBeforeClass()
    {
        $host = $_ENV['MONGO_PORT_27017_TCP_ADDR'] ?? 'localhost';
        $port = $_ENV['MONGO_PORT_27017_TCP_PORT'] ?? 27017;

        self::$collection = (new Client(sprintf('mongodb://%s:%d', $host, $port)))->selectCollection('api_test', 'wallet');
    }

    protected function setUp()
    {
        $this->wallet = new MongoWallet(self::$collection, 'john.doe');

        self::$collection->drop();
    }

    protected function tearDown()
    {
        self::$collection->drop();
    }

    /**
     * @test
     */
    public function it_retrieves_empty_balance()
    {
        $this->assertEquals(0, $this->wallet->balance()->toInteger());
    }

    /**
     * @test
     */
    public function it_retrieves_balance()
    {
        $this->initDocumentBalance(10);

        $this->assertEquals(10, $this->wallet->balance()->toInteger());
    }

    /**
     * @test
     */
    public function it_deposits_credit()
    {
        $this->wallet->deposit(Credit::fromInteger(25));
        $this->assertDocumentBalanceEquals(25);

        $this->wallet->deposit(Credit::fromInteger(15));
        $this->assertDocumentBalanceEquals(40);
    }

    /**
     * @test
     */
    public function it_withdraws_no_credit()
    {
        $this->wallet->withdraw(Credit::blank());
        $this->assertDocumentBalanceEquals(0);
    }

    /**
     * @test
     */
    public function it_withdraws_credit()
    {
        $this->initDocumentBalance(50);

        $this->wallet->withdraw(Credit::fromInteger(15));
        $this->assertDocumentBalanceEquals(35);

        $this->wallet->withdraw(Credit::fromInteger(25));
        $this->assertDocumentBalanceEquals(10);

        $this->wallet->withdraw(Credit::fromInteger(10));
        $this->assertDocumentBalanceEquals(0);
    }

    /**
     * @test
     */
    public function overdraft_must_not_be_possible()
    {
        $this->initDocumentBalance(10);

        $this->expectException(InsufficientFunds::class);
        $this->expectExceptionMessage('Insufficient credit: 5.');

        $this->wallet->withdraw(Credit::fromInteger(15));
    }

    private function assertDocumentBalanceEquals(int $balance)
    {
        $doc = self::$collection->findOne(['_id' => 'john.doe']);

        $this->assertInstanceOf(BSONDocument::class, $doc);
        $this->assertEquals($balance, $doc['balance']);
    }

    private function initDocumentBalance(int $balance)
    {
        self::$collection->insertOne(['_id' => 'john.doe', 'balance' => $balance]);
    }
}
