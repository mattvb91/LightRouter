<p align="center"><b>LightRouter</b></p>

<p align="center">
  <img src='https://coveralls.io/repos/github/mattvb91/LightRouter/badge.svg?branch=master'/>
  <img src="https://travis-ci.org/mattvb91/LightRouter.svg?branch=master">
  <img class="latest_stable_version_img" src="https://poser.pugx.org/mattvb91/lightrouter/v/stable">
  <img class="total_img" src="https://poser.pugx.org/mattvb91/lightrouter/downloads">
  <img class="latest_unstable_version_img" src="https://poser.pugx.org/mattvb91/lightrouter/v/unstable">
  <img class="license_img" src="https://poser.pugx.org/mattvb91/lightrouter/license">
</p>

## LightRouter

Lightweight PHP router class. This is a test project. If you need a reliable & fully
tested solution please check out FastRoute or AltoRouter.

### Basic Usage

```php
$router = new mattvb91\LightRouter\Router();
$router->addRoute('/', MyController::class);
$router->run();
```

### Defining Routes

To add new routes use the ```$router->addRoute()``` method. If no action is defined
it will default to ```index``` which will be needed on your controller.

You will need to pass the route path & either a controller or a closure. If you do
not specify an action it will default to 'index' which will be required on 
your controller.

```php
$router->addRoute('/', MyController::class);
$router->addRoute('/contact', MyController::class, 'contact');

$route->addRoute('/hello', function() {
    echo 'Hello world';
});
```

### Defining parameters

Passing parameters into your controller actions can be done using a `:parameter` attribute
in your route definition:
```php
$router->addRoute('/view/:param', MyController::class);

```

Your method parameter must match the defined route parameter. In our example above 
our controllers ```view``` method would look like this:

```php
public function view($param)
{
}
```

### Automatically Injecting Models

If you are using the ```LightModel``` ORM package for your DB models you can automatically
inject the associated model by specifying the instance type in your 
controller action:

```php
//Your route definition
$router->addRoute('/user/view/:user', UserController::class, 'view');

//In your UserController::class
public function view(User $user)
{
    //$user is already fully loaded for you.
}
```
If you are using your own ORM or other DB model class you can also implement 
the ```LightRouteModelInterface::class``` in your custom model which expects
you to return the fully loaded instance. 

LightRouter will pass the associated $key from your route into the ```getForLightRoute``` method:

```php
public class CustomModel implements LightRouteModelInterface
{
    public static function getForLightRoute($routeParam): LightRouteModelInterface
    {
        //Use $routeParam to load your model class and return the instance
    }
}
```