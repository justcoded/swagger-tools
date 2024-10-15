# Swagger Tools

This repository contains an examples of OAS/Swagger API documentations with several tools and a guide to create 
good API documentation and use it to build a Mock server.

## Why to use API documentation?

When two different teams develop backend and frontend separately - they both need a document stated the request
and response data format. Of course, they make a deal in Slack for example and try to remember it. 
However, in this way your product become a "black box", which is highly difficult to support.

As a solution we propose to create a structured API documentation, which is not dependent on some specific technology. 
I mean that you don't need to learn some PHP or JavaScript to create such documentation.
Nowadays, there are two popular formats: [OAS](https://swagger.io/specification/) (Open API Specification) and 
[RAML](https://raml.org/) (RESTful API Modeling Language). RAML is pretty cool, but OAS has bigger community and OAS 
became our choice. And unfortunately, we need to use outdated OAS2 format (aka "Swagger"), because OAS3 is 
not supported by numerous tools you will probably need it future.

## Contents

You can find in this repo:

* Examples of swagger API documentation in OAS2, OAS3 in Yaml format. _It's a multi-document structure with cross-docs references, 
to keep things DRY (Don't Repeat Yourself)_
* [Swagger UI](https://swagger.io/tools/swagger-ui/) viewer for API documentation
* PHP tool to **merge multi-document yaml** to a single document. _Unfortunately available tools works only with single documents._
* PHP tool to **generate fake object examples** with [Faker](https://github.com/fzaninotto/Faker) library.

## Writing Docs

### Setup project

To start with - you can clone this repository to some webserver. It uses static html and javascript, so you don't need any
special server configuration. You will need composer package manager on the server.

```bash
git clone https://github.com/justcoded/swagger-tools.git /path/to/docroot/swagger
cd /path/to/docroot/swagger
composer install
```

That's it, you can open `http://mydomain.com/swagger/` in browser and you will get a Swagger UI with our sample doc.

_Or you can try to check swagger UI running nginx with docker like this:_
```bash
docker run -p 8080:80 --name swagger-tools -v ./:/usr/share/nginx/html:ro nginx
```

### Create your docs

Now let's go to the `examples` folder. You can copy some folder to start with. 
After that you will need to update `index.html` to point to your new docs by default:

```js
<script>
window.onload = function() {

  // Build a system
  const ui = SwaggerUIBundle({
	url: "./examples/jsonapi/swagger.yaml",
	...

``` 

As already mentioned we use OAS2 format, specification can be found here:

* https://swagger.io/docs/specification/2-0/basic-structure/
* https://github.com/OAI/OpenAPI-Specification/blob/master/versions/2.0.md

If you are new to the swagger format you will need to learn such declarations:

- Definitions Object (for Models and Objects)
- Path Object - for routes
- Parameter Object - for defining parameters
- Response Object - for response

Docs structure we propose to use:

```
- routes/			# Routes/Paths definitions grouped by section
	- auth.yaml
	- users.yaml

- models.yaml			# Models/Requests definition
- params.yaml			# Params, which are similar for paths in different routes groups
- responses.yaml 		# General responses definitions like 204, 401, 403, 404, 422, 500, etc.
- swagger.yaml 			# Main file, which includes other files
```

Because of limitations of OAS2 format we need to list ALL routes inside main file:

```yaml
paths:
  /auth/sign-in:
    $ref: "routes/auth.yaml#/paths/login"
  /auth/verify:
    $ref: "routes/auth.yaml#/paths/verify"
  /auth/password-reset:
    $ref: "routes/auth.yaml#/paths/resetPassword"
  ...
```

Having the examples I think all other things are self-explanatory.

### Swagger Editor

We recommend to use WebStorm or PHPStorm (or any other Jetbrains IDE) to edit swagger yaml files. To have autocompletions 
on swagger identifiers and references you will need to install plugin called [Swagger Plugin](https://plugins.jetbrains.com/plugin/8347-swagger-plugin).

### JSON format

When you develop an API it's always a hard decision on what format JSON should be looks like. To solve this problem we 
suggest to follow JSONAPI specs: [http://jsonapi.org/](http://jsonapi.org/)

In general, it defines that we need to transfer data in a format like this:

```js
{
	"data": [{ // collection or resource itself
		"type": 'resource type',
		"id": 'some id',
		"attributes": {
			// resource attribute
		},
		"relationships": {
			'relation name': {
				"data": { "type": "resource type", "id": "some id" }
			}
			// ...
		}
	}],
	"included": [{ // data for related resources
        "type": "related resource type",
        "id": "some id",
        "attributes": {
        	// related resource data
        }
        // ...
    }],
	"meta": {
		// some other data like pagination info, tokens, etc.
	}
}
```

This format is good for JavaScript libraries such as React.js, Vue.js and Angular, because it's super easy to populate scope 
based on resource types. 

JSONAPI has a lot of ready-to-use implementations: http://jsonapi.org/implementations/

#### JSONAPI Format For Non-resource Requests

Unfortunately, this specification doesn't answer how to form non-resource requests. Usually these are some actions, for example "search".

We suggest to define that we can transfer a document-type model of type "userInput", which can vary from route to route.
Looks like this:

```js
{
	"data": {
		"type": "userInput",
		"attributes": {
			// key/value pairs of user input data
		}
	}
}
```  

## PHP Tools

* Merge multi-document swagger YAML documentation
* Generate fake data to enums for mock servers

### Merge tool

	./cli/swagger-merge.php examples/v2.0/swagger.yaml > merged-swagger2.yaml

### Faker tool

Add to your properties x-faker attribute:

	swagger: '2.0'
    definitions:
      User:
        type: object
        properties:
          id:
            type: integer
            format: int64
            minimum: 1
          email:
            type: string
            format: email
            x-faker: email
          first_name:
            type: string
            x-faker: firstName
          last_name:
            type: string
            x-faker: lastName
            
Run command:

	./cli/swagger-faker.php examples/merged/swagger2.yaml -n10 -r -c > swagger-faked.yaml

You will get:

	definitions:
      User:
        type: object
        properties:
          id:
            type: integer
            format: int64
            minimum: 1
          email:
            type: string
            format: email
            x-faker: email
            enum:
              - muhammad.schuster@yahoo.com
              - hansen.rudy@nolan.com
              - nlittel@hotmail.com
              - gleason.araceli@witting.com
              - sreichert@hotmail.com
              - swift.dayna@kris.com
              - russel55@gmail.com
              - dicki.emery@hotmail.com
              - morris77@hotmail.com
              - lprice@yahoo.com
          first_name:
            type: string
            x-faker: firstName
            enum:
              - Denis
              - Donna
              - Elissa
              - Brandyn
              - Dino
              - Linda
              - Sadye
              - Kenneth
              - Michel
              - Theo

		  ...

	    required:
	  	- id
	  	- email
	  	- first_name
	  	- last_name
	  	- profile

# Using this repo as dependency

Of course, you can add this repository as dependency to your project, symlink swagger ui assets to your public repository
and create your template inside your framework to print swagger ui interface, pointing to some generated doc.

As an example we created Yii extension, wrapping our PHP tools: https://github.com/justcoded/yii2-swaggerviewer
