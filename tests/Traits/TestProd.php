<?php


namespace Tests\Traits;


trait TestProd
{
        protected function skipTestIfNotProduction($message = '')
        {
            if(!$this->isTestingProd()){
                $this->markTestSkipped($message);
            }
        }

        protected function isTestingProd()
        {
            return env('TESTING_PROD') !== false;
        }
}
