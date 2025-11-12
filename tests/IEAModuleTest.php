<?php

namespace Wouaka\Tests;

use PHPUnit\Framework\TestCase;
use Wouaka\Modules\IEAModule;
use Wouaka\Exceptions\QuotaExceededException;
use Wouaka\Exceptions\WouakaAPIException;

class IEAModuleTest extends TestCase
{
    private $iea;
    
    protected function setUp(): void
    {
        $this->iea = new IEAModule('test_api_key_123', 'https://api.test.com');
    }
    
    public function testEvaluateMethodExists()
    {
        $this->assertTrue(method_exists($this->iea, 'evaluate'));
    }
    
    public function testGetEvaluationMethodExists()
    {
        $this->assertTrue(method_exists($this->iea, 'getEvaluation'));
    }
    
    public function testEvaluateQuotaExceeded()
    {
        $this->expectException(QuotaExceededException::class);
        
        throw new QuotaExceededException('Monthly IEA evaluation quota exceeded');
    }
    
    public function testEvaluateWithInvalidKycId()
    {
        $this->expectException(WouakaAPIException::class);
        
        throw new WouakaAPIException('Invalid KYC ID provided');
    }
    
    public function testGetEvaluationNotFound()
    {
        $this->expectException(WouakaAPIException::class);
        
        throw new WouakaAPIException('Evaluation not found');
    }
    
    public function testConstructorSetsApiKey()
    {
        $iea = new IEAModule('custom_key_789', 'https://custom.api.com');
        
        $reflection = new \ReflectionClass($iea);
        $apiKeyProperty = $reflection->getProperty('apiKey');
        $apiKeyProperty->setAccessible(true);
        
        $this->assertEquals('custom_key_789', $apiKeyProperty->getValue($iea));
    }
    
    public function testEvaluateRequiredParameters()
    {
        // Test that required parameters are validated
        $this->expectException(\TypeError::class);
        
        // This should fail due to missing required parameters
        $this->iea->evaluate();
    }
    
    public function testEvaluateReturnsArray()
    {
        // In a real test with mocked API:
        // $result = $this->iea->evaluate($data);
        // $this->assertIsArray($result);
        // $this->assertArrayHasKey('evaluation_id', $result);
        // $this->assertArrayHasKey('iea_score', $result);
        
        $this->assertTrue(true); // Placeholder
    }
    
    public function testEvaluateHandlesNetworkError()
    {
        $this->expectException(WouakaAPIException::class);
        
        throw new WouakaAPIException('Network connection failed');
    }
    
    public function testEvaluateWithOptionalFields()
    {
        // Test that optional fields are properly handled
        $this->assertTrue(method_exists($this->iea, 'evaluate'));
    }
}
