# MuSée

![Musée](https://github.com/panyushov/musee/blob/master/media/musee-hero.png "Musée")

MuSée is a little yet very powerful tool for sitemap generation built with scalability in mind. 

While the number of pages and content of your web app grows exponentially it becomes very difficult for search engines such as Google or Bing to crawl and index your pages.
  
Here where Sitemaps come to save you!
  
In its essence a sitemap is just an XML file containing all the possible links to the pages of your website together with other useful information like crawling priority, changing frequency and even last modified date of the content on the page. Complete sitemap protocol can be found [here](https://www.sitemaps.org/protocol.html).

MuSée uses a one-to-many relational generation approach based on direct access and manipulation of web resources. In other words, it fetches relational data from given REST API and organizes it logically in the same sitemap. All these using powerful [Laravel Queue engine](https://laravel.com/docs/queues) to process each request in a non-blocking manner and as fast as your infrastructure can do it!


### Installation

#### Docker

You gonna need Docker installed and running on your platform. After that you can clone this repository, change directory inside it and launch Docker compose:

```
git clone https://github.com/panyushov/musee.git
cd musee
docker run --rm -v $(pwd):/app composer install
docker-compose up --build
```
And that's it!
Once Docker will finish building image and launching all containers you will find MuSée in your browser at: [http://localhost:9345](http://localhost:9345)

#### Using a local host

First of all you will have to install all MuSée dependencies using Composer.
Just clone the repository, change directory and launch Composer install command:

```
git clone https://github.com/panyushov/musee.git
cd musee
composer install
```

The second and very important step is to launch the app initialization command:

```
php artisan app:init
```

This command launches background workers that process Laravel Queue jobs. Remember to execute it every time your machine restarts.

## Running the tests

MuSée uses PHPUnit as its primary testing framework.
After the initial setup, you will be able to launch all tests by simply calling PHPUnit binary installed by the Composer:

```
vendor/bin/phpunit
```

## Built With

* [Laravel](https://laravel.com/docs) - The Web Framework used
* [Composer](https://getcomposer.org/doc/) - Dependency Manager
* [PHPUnit](https://phpunit.de/documentation.html) - The PHP Testing Framework

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details
