<?php


namespace Tests\Unit\Rules;


use App\Rules\GenresHasCategoriesRule;
use Mockery\MockInterface;
use Tests\TestCase;

class GenreHasCategoriesRuleUnitTest extends TestCase
{
    public function testCategoriesIdField()
    {
        $rule = new GenresHasCategoriesRule(
            ['1','1','2','2']
        );
        /** Quero pegar um atributo que é privado, por isso vou usar a classe ReclectionClass */
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('categoriesId');
        $reflectionProperty->setAccessible(true);

        $categoriesId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2],$categoriesId);
    }

    public function testGenresIdValue()
    {
        $rule = $this->createRuleMocke([]);

        $rule
            ->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturnNull();

        $rule->passes('', [1,1,2,2]);
        /** Quero pegar um atributo que é privado, por isso vou usar a classe ReclectionClass */
        $reflectionClass = new \ReflectionClass(GenresHasCategoriesRule::class);
        $reflectionProperty = $reflectionClass->getProperty('genresId');
        $reflectionProperty->setAccessible(true);

        $genresId = $reflectionProperty->getValue($rule);
        $this->assertEqualsCanonicalizing([1,2],$genresId);
    }

    public function testPassesReturnsFalseWhenCategoriesPrGenresIsArrayEmpty()
    {
        /** @var GenresHasCategoriesRule $rules */
        $rules = $this->createRuleMocke([]);
        $this->assertFalse($rules->passes('',[1]));

        /** @var GenresHasCategoriesRule $rules */
        $rules = $this->createRuleMocke([1]);
        $this->assertFalse($rules->passes('',[]));
    }

    public function testPassesReturnsFalseWhenGetRowsIsEmpty()
    {

        $rule = $this->createRuleMocke([]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect());
        $this->assertFalse($rule->passes('',[1]));
    }

    public function testPassesReturnsFalseWhenCategoriesWithoutGenres()
    {
        $rule = $this->createRuleMocke([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect(['category_id' => 1]));
        $this->assertFalse($rule->passes('',[1]));
    }

    public function testPassesIdValid()
    {
        $rule = $this->createRuleMocke([1,2]);
        $rule->shouldReceive('getRows')
            ->withAnyArgs()
            ->andReturn(collect([
                    ['category_id' => 1],
                    ['category_id' => 2],
                    ['category_id' => 1],
                    ['category_id' => 2]
                ]
            ));
        $this->assertTrue($rule->passes('',[1]));
    }



    protected function createRuleMocke(array $categoriesId): MockInterface
    {
        return \Mockery::mock(GenresHasCategoriesRule::class,[$categoriesId])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }
}
