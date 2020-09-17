<?php
namespace Cichowski\Validator;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider
{
    const VALIADTORS_NAMESPACE = 'Cichowski\Validator\Validators\\';
    
    const VALIADTORS_PATH = __DIR__ . '/Validators/';
    
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {                
        $messages = array();
        
        //loading localization to be avaliable in application
        $this->loadTranslationsFrom(__DIR__ . '/lang', 'cichowski');                
        
        foreach ($this->getAllValidatorClasses() as $className) {

            $key = strtolower($className);
            
            //registering validators
            $this->app['validator']->extend($key, self::VALIADTORS_NAMESPACE . $className . '@validate');
            
            //setting messages for validators
            $messages[$key] = app('translator')->has('validation.' . $key) ? trans('validation.' . $key) : trans('cichowski::validator.' . $key);                       
        }                        
        
        //loading messages for validators
        $this->app['validator']->resolver(function($translator, $data, $rules, $messages) use ($messages) {

            return new \Illuminate\Validation\Validator($translator, $data, $rules, $messages);
        });        
    }
    
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register() {}
    
    /**
     *  Lists all validator class name 
     *  (read from filenames in dir)
     * 
     *  @return array
     */
    public function getAllValidatorClasses()
    {
        $baseNames = array();        
                        
        foreach (new \FilesystemIterator(self::VALIADTORS_PATH) as $fileInfo) {
            
            if ($fileInfo->isFile() && $fileInfo->getExtension() == "php") {
                
                array_push($baseNames, $fileInfo->getBasename('.php'));
            }
        } 
        
        return $baseNames;   
    }
}
