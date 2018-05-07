# Swagger Tools

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

	./cli/swagger-faker.php examples/merged/swagger2.yaml -n10 -r > swagger-faked.yaml

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
