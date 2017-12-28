Wordpress Starter (Integration: wp_system)

This is the official documentation of le0daniel/wp_system

It is based on a custom version of Bedrock (roots.io) and integrates the wp_system. The goal of the system is to have modern PHP tools for creating your custom WP Theme. It includes:

- The powerful Laravel 5 Service Container (Dependency injection, Type Hinting), 
- Symfony Twig 2 as Templating engine
- Symfony Console for interacting with the App
  - Symfony Process 
- Clear File Structure (almost same as Bedrock, except web -> public)
  - Must use plugins
  - .env config with different config files per environment
  - composer for dependency management
- Mix (webpack) for versioning and compiling assets (js|css)



File Structure

    YourProject/
    |—— cache/
    |   |—— rendered/
    |   |—— twig/
    |—— config/
    |   |—— application.php
    |   |—— system.php
    |   |—— environments/
    |       |—— development.php
    |       |—— production.php
    |       |—— staging.php
    |—— public/
    |   |—— app/
    |       |—— mu-plugins/
    |       |—— plugins/
    |       |—— themes/
    |       |—— uploads/
    |       |—— maintenance.php
    |   |—— wp/
    |   |—— wp-config.php
    |   |—— index.php
    |   |—— .htaccess
    |—— storage/
    |   |—— log/
    |—— vendor/
    |—— node_modules/

The web root is public 

For better organizaion wp-content is in app and Wordpress is in wp and all dependencies, configurations, files are kept out of the web root itself.

Theme Structure

    YourProject/public/app/themes/YourTheme
    |—— App/
    |   |—— PostTypes/
    |   |—— ServiceProvider/
    |   |—— ShortCodes/
    |   |—— WordPressExtender.php
    |—— resources/
    |   |—— assets/
    |       |—— js/
    |       |—— scss/
    |   |—— lang/
    |   |—— views/
    |       |—— components/
    |       |—— layouts/
    |       |—— pages/
    |       |—— shortcodes/
    |—— static/
    |—— functions.php
    |—— style.css

The goal is to keep a clear structure in the Theme. All files which are accessed over the internet should be in the static folder. By default, webpack compiles Js / Scss (script.js/style.scss) to static/

The functions.php file contains the configuration of the Application and some Bindings. Configure your theme here. 

The WordPressExtender.php wraps some common tasks (shortcodes including visual composer componets, navs, posttypes) and simplifies them by giveing them a clear structure.

All logic should be placed into the default wordpress templates (seen as controllers) and then passed to Twig to render (more in the view section).



Installation

Requirements

- composer
- PHP >= 7

To install in the directory YourProjectName run:

 composer create-project le0daniel\wp_starter YourProjectName

After the installation cd YourProjectName and run the installer php installer

This will help you create your theme with the correct namespace, connect to the DB. After this, take a look at the options in your .env file.

    # Enables reloding of assets if you run 'npm run watch'
    HOT_RELOAD=false
    
    # Exposes the duration the request took
    EXPOSE_DURATION=false
    
    # Disable setting of security headers like 'X-FRAME-OPTIONS'
    DISABLE_SECURITY_HEADERS=false
    
    # Your environment and URL
    WP_ENV=development
    WP_HOME=http://mywordpress.localhost
    
    # Salts, generate them using wp dotenv 

After you're done, it's important to run composer dump-autoload because psr-4 autoloading paths have changed

Now you are ready



functions.php

First of all, namespaces are used and should be used where possible. The file contains basic configuration, like $theme_name  $view_root  and the usual wordpress theme setting. 

More important are the bindings. By default everything is resolved through the Laravel Service Container. You can overwrite everything the app uses. 

You can either add your bindings into the functions file or you can simply create and register (config/system) a ServiceProvider.

Following aliases can easylie be overwritten to add more specific functionality. Your class should always extend the base class to keep functionality. Take a look at the individual pages for more information about what they provide. A lot is also explained in the view section.

    /**
     * Get the dependency container through the app helper 
     *
     * @var le0daniel\System\App $app
     */
    $app = app();
    
    /* Create your individual bindings */
    $app->bind('wp.context',	YourClass::class); // le0daniel\System\WordPress\Context 
    $app->bind('wp.metafield',	YourClass::class); // le0daniel\System\WordPress\MetaField
    $app->bind('wp.page',		YourClass::class); // le0daniel\System\WordPress\Page
    $app->bind('wp.post',		YourClass::class); // le0daniel\System\WordPress\Post
    $app->bind('wp.site',		YourClass::class); // le0daniel\System\WordPress\Site
    $app->bind('wp.user',		YourClass::class); // le0daniel\System\WordPress\User
    $app->bind('wp.shortcode',	YourClass::class); // le0daniel\System\WordPress\ShortCode

The view configuration options are explaind below in the view section.



View

The view class (le0daniel\System\View\View) is the wrapper around twig. It contains easy methods to render, add functions and filters, set include paths for twig. The view object is registered as a singleton in the Service Container. Following configuration options / methods exist:

    /** 
     * The view object can be gotten through the container (app), 
     * resolved throuht the resolve helper or 
     * with the view helper called without any arguments
     *
     * Usually use the view() helper!
     *
     * @var le0daniel\System\View\View $view 
     */
    $view = app()->get('view');
    $view = app()->make('view');
    $view = resolve('view');
    $view = view();
    
    /**
     * Sets the view root dir. 
     * MUST be set before anything can be rendered. Cannot be changed after 
     * something has been rendered.
     */
    $view->setRootDir('path/to/your/views');
    
    /**
     * This adds an alias (called: @alias in twig template)
     * Those paths can be added any time.
     */
    $view->addIncludePath('path/to/your/path','alias')
      
    /**
     * Share Data with all views, can be accessed by every template!
     */
    $view->share('key','value');  
    
    /**
     * Add Function (& addFilters for Filters)
     * Documentation of Available filters and Functions found in twig official Docs
     */
    $view->addFunction(
      /* callable */ '\le0daniel\myfunction',
      /* string   */ 'FunctionAliasTemplate',
      /* array    */ ['Params according to twig documentation']
    );
    
    /**
     * Render
     * Renders a view to html
     *
     * @return string $html
     */
    $string_html = $view->render(
      'template.twig',
      ['Data'=>'Array'],
      /* Default: true  */ $with_context,
      /* Default: false */ $force_plain_cache,
      /* Default: -1    */ $cache_duration,
    ); 
    
    /**
     * Show
     * Renders and outputs a view
     *
     * if terminate is true, the script quits after this.
     * @return void
     */
    $view->show(
      'template.twig',
      ['Data'=>'Array'],
      /* Default: true  */ $terminate
    ); 
    
    /**
     * The view->show() can also be achieved using the view helper function
     * The helper forces a termination of the script!
     * 
     * If called without arguments, the helper returns the view object
     *
     * Simply use:
     */
    view('template.twig',['array'=>'data']);



Following filters are available for all templates:

- theme_path  returns url relative to theme root
- static_path  returns url relative to the themes static folder. This should be used to include files any file
- mix(rewrite=false)  returns versioned url (with id query) if possible, optionally use mix(true) to rewrite the url into a more cache friendly format: .../static/##key##/script.js 
- t or translate translate using the default context set in functions.php file
- eclipse(lenght=55) shortens a string to a max lenght, default is 55 
  You can pass any lenght using eclips(200)



Following functions are available for all templates:

- function(callable) calls a function (usecase mostly for Wordpress functions) and returns the output or echo. The output is always escaped, so if you want to use the output as safe HTML be sure to add the raw filter. Example: {{ function('my_nav_function') | raw }}
  By default this is used to call wp_head and wp_footer 
- field(name,id=false) returns a ACF field value (using get_field)



Shortcodes (including VC)

Shortcodes facilitate (and stop the repetitive work of adding shortcodes in wordpress) by wrapping them into an object.

To generate a shortcode, simply run following command: php console make:shortcut this will generate all the needed files for your shortcode (and VC component if needed). This generates a controller, a template file and a scss file. The file structure is as followed relative to your theme root:

    Based on the name 'My Shortcode'
    Class: camelcase(name) = MyShortcode
    Slug : snakecase(name) = my_shortcode
    
    
    |—— App/
    |    |—— ShortCodes/
    |        |–– MyShortcode.php
    |–– resources/
    |    |—— assets/ 
    |        |–– scss/
    |            |–– my_shortcode.scss
    |    |—— views/ 
    |        |—— shortcodes/
    |            |–– my_shortcode.twig

In the following example we take a look at the different configuration methods for a Shortcut with the name: 'My Shortcut'. The generated controller File (MyShortcode.php) will look as following:

    namespace Themes\MyThemeName\App\ShortCodes;
    
    use le0daniel\System\WordPress\ShortCode;
    
    /**
     * Class MyShortcode (Controller)
     */
    class MyShortcode extends ShortCode
    {
        /**
         * The name (& slug) should not be changed as they are used to find the
         * template paths!
         */
        protected $name = 'My Shortcode';
        protected $slug = 'my_shortcode';
    }

The name is irrelevant for a Shortcode itself, as wordpress does not use it. It is mainly used if you create a Visual Composer Component as the human readable name is displayed. The slug on the other hand is required and used to retrieve the template file and name the component for wordpress. 

To understand how this words, we take a look at how a shortcode is registered:

    namespace le0daniel\System\WordPress;
    
    class ShortCode {
      
      
      /** returns the shortcut name */
      protected function getTagName(): string {
        return $this->slug;
      }
      
      /** returns the shortcode array */
      public function toShortcode(): array {
        return [
          $this->getTagName(),
          [$this,'render']
        ];
      }
      
    }

So the shortcuts are then registered using add_shortcode( ...$shortcode->toShortcode() ). If you want to modify the tag name, simply overwrite the getTagName method in your controller.

The callback for the shortcode ist the render method and can't be overwritten (final method). You can tho intercept the attributes given to the template:

    class MyShortcode extends ShortCode
    {
      
      /**
       * This method is used to normalize the given attributes from
       * the shortcode. All attributes are then returned in as an
       * array for your template:
       * 
       * [ 'content'=>'do_shortcode(content)', 'key' => 'value', ... ]
       *
       * This is where you can add additional Attributes.
       */
      protected function prepareAttributes( $attributes, $content ):array{
        /** @var array $attributes */
        $attributes = parent::prepareAttributes( $attributes, $content );
        
        /* Add and manipulate attributes here */
        $attribute['test'] = 'awesome';
        
        return $attributes;
      }
      
    }

Following options are available for your shortcode class

    class MyShortcode extends ShortCode
    {
      
      /**
       * Used for getting the template
       * Template follows format: {$namespace}/{$slug}.{$extension}
       * Ex: '@shorts/my_shortcode.twig'
       */
      protected $namespace = '@shorts';
      protected $extension = 'twig';
      
      /**
       * Used to filter attributes
       * Filters array to only contain certain
       * keys, if empty, all keys are allowed
       *
       * Usefull because wordpress will pass any
       * attributes that are specified by the user
       */
      protected $only = [];
      
      /**
       * By default, the shortcodes are rendered without
       * Context, means they don't have access to the
       * site, page, user variables
       */
      protected $render_with_context = false;
      
      /**
       * The content passed to the prepareAttributes methods
       * is passed through do_shortcode() by default before it
       * is added to the attributes array. You can disable
       * that if you don't expect content with shortcodes.
       */
      protected $do_shortcode_on_content = true;
    }

Thats all about shortcodes. Rendering, renders the template with the available attributes.

Visual Composer Component

If your component is a Visual Composer Component, you can easily create them as well. For that, your Shortcode needs to implement the le0daniel\System\Contracts\VisualComposerComponent contract and use the le0daniel\System\Traits\isVisualComposerComponent trait. This gives some more configuration options:

    class MyShortcode extends ShortCode implements VisualComposerComponent
    {
      use isVisualComposerComponent;
      
      /* Required */
      protected $category    = "Stylish Components";
      protected $description = "Adds some color to your Page";
      protected $group       = "le0daniel";
      
      /* Not Required */
      //protected $icon        = "/app/myicon.png";
      //protected $weight      = 10;
      
      /**
       * Create VC Parameters in a Fluid way!
       */
      public function createVisualComposerParams ( ParameterHelper $param ){
        
        /**
         * Add a parameter in a fluid way. The names are the same as given
         * the ones from vc_map, except in camelcase and add as prefix
         * textarea_html => addTextAreaHtml
         * textfield	 => addTextField ...
         * 
         * Every parameter needs a name (key) which will then be given to the 
         * shortcut (Exception is addTextAreaHtml which will always be called content)
         *
         * Every Parameter requires a Heading, Description. Both are translated by default
         * If you need to set other parameter settings, use set('key','value')
         */
        $textarea = $param->addTextAreaHtml();
        $textarea->addHeading('The Content')
        $textarea->addDescription('Requires no name because it is the content')
        $textarea->setGroup('Content');
        
        // Or fluid
        $param->addTextField('name')
          ->addHeading('Enter an awesome name')
          ->addDescription('You may enter an awesome name or enter a normal name')
          ->setGroup('Name')
          ->set('holder','div')
          ->set('class','test_class');
      }
    }

With that, you can easily create vc components in a fluid way. The configuration of shortcodes is cached for better performance. This means, if you change params in a production environment you need to flush the cache after that using php console clear:cache 


