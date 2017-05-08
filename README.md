# API for creating events using spatial mysql DB and Zend Framework 2

This is a sample code. No information regarding passwords or location is provided. The path names are fiction.

Characteristics:

* Zend Framework 2
* The user can create events via POST or use eventful API for creating dummy events.
* The user can GET all points of interests (events information including latitude and longitude) around the given location. For example we can display in a mobile app map using the current location and finding its nearest neighbors.
* Use spatial DB

Structure (relevant files):

* /Events/module/Application/config/module.config.php -> router
* /Events/config/autoload/global.php -> DB info
* /Events/module/Application/Module.php
* /Events/module/Application/src/Application/ -> MVC


Applications:

*The application for this source code is for storing/fetching events information via API.
