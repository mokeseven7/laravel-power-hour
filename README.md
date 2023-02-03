# GCE Employee API 

This readme contains setup instructions, sample requests, and information on the authentication mechanism I chose. There is 100% test coverage which is validated on every commimt. The environment is fully containerized, and offeres a few different options for setup. This is my favorite dev environment setup at the moment becuase of how flexibile it is, and how quickly it allows someone to jump into a codebase. 


> Dont forget check out the `actions` tab of this repo. There is some cool stuff started. There would be some ever more cool stuff if I had more time ;)

## Environment Setup


**Option One - Hosted Codespace**   

This is the fastest and easiest way. Either shoot me your github name so I can add you, and you can spin a codespace up directly in this repo, or fork it, and follow the same instructions below. 

![Code Spaces](.github/codespaces.png)



Once you click the plus button, the container is lauched, and you'll be prestend with a web based version of Visual Studio Code. Since I am prebuilding the containers on each commit, (check out the actions tab if interested), the spin up is instant. Without pre-builds it takes about 4-6 mins, buts compiling php and extensions from source, so that time isnt actually that bad. 


There is a build command that should run automatically when you open the codespace. You can verify with the one route with no auth `curl http://localhost:8000/ping`, you should get `pong` back right away. If you dont, just run `make install` and that will fix it. 


**Option Two - Using Docker & VS code Locally.**

Basically the exact same idea, only your using your machine as the container host instead of one of githubs. It uses the exact same files to build the environment. 

As a prequesite, ensure you have the following installed on your mechine. 

Docker Desktop - https://www.docker.com/products/docker-desktop/  
Visual Studio Code - https://code.visualstudio.com/  
Dev Containers Exension - https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers

**Bonus:**
If you also install the [codespaces extension](https://marketplace.visualstudio.com/items?itemName=GitHub.codespaces) you'll be given an extra context menu option in both the web and desktop version of VS Code, allowing you to effortlessly switch between the web based container, and a container you run locally with the click of a button: 
![Codespaces Extension](.github/codespaces2.png)

Step 1. clone this repository to your local machine:

```
$ git clone git@github.com:mokeseven7/gce.git
```

Step 2. Using a method of your choice, launch the cloned repository in Visual Studio Code.

Step 3. Install the "Dev Containers" extension, and optionally the codespaces extension mentioned above. 

Step 4. Using the command menu which will either be `cmd + shift + p` or `ctrl + shift + p`, select the `Rebuild and Reopen in Container Option`, which is provided by the dev containers extension:

![Dev Containers](.github/devcontainers.png)


 ## API Usage Overview

 Reguardless of which option you went with, when the container was build, all of the commands required to get you up and running should have already been run. This includes, migrating the database to the mariadb service provided by the docker-composer file inside of the container, as well as seeding the database, and starting a local development server thats available at port 8000 on localhost loopback interface. 

Verify this functionality by making a simple, unauthenticated curl to the ping endpoint:

```
curl http://localhost:8000/api/ping
```

You should immidiatly recieve a plain text response of `pong`. If so, you're good to go. 

If for some reason you don't get that response, just run the `make install` command manually and that should fix it. 


## Authentication 

This api implements scoped based token authentication. Tokens belong to users, which represent consumers of the API. This could be a client application or a 3rd party.

A user has a set of credentials they are able to exchange for scoped tokens, which are permissioned into specific resources or actions. The concept heavily influenced from the token based authentication scheme github came up with, and uses a laravel package called "sanctum". 



> Create a user user with the `php artisan user:create` helper command. 

![create user](.github/create_a_user.png)

>Take note of the basic auth string, or the plaintext values you entered into the command. 


### Request A Token
```sh
curl -s -X POST http://localhost:8000/api/tokens/employee \
    -H "Authorization: Basic YXBpY29uc3VtZXI6YWRtaW4=" \ 
    -H "Accept: application/json" | jq '.' 
```

```json
{
    "token": "10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo",
    "accessToken": {
      "name": "apiuser",
      "abilities": [
        "employee:read",
        "employee:write"
      ],
      "expires_at": null,
      "tokenable_id": 3,
      "tokenable_type": "App\\Models\\User",
      "updated_at": "2023-02-02T20:31:58.000000Z",
      "created_at": "2023-02-02T20:31:58.000000Z",
      "id": 10
    },
    "plainTextToken": "10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo"
}

```

You can see the token was issued, with a set of specific scopes, or abilities, which directly correspond to resources or actions. If you look in the routes file, you can see how the middlware is assigned to routes based on the scopes. 

```php
Route::get('/', [EmployeeController::class, 'index'])->middleware(['auth:sanctum', 'abilities:employee:read']);
```

## API Resource Referance 

>**GET /api/employees**
```bash
$ curl -s http://localhost:8000/api/employees -H "Authorization: Bearer <token>"
```

```json
[
  {
    "id": 1,
    "name": "Britney Effertz",
    "email": "ruecker.walton@example.org",
    "employee_id": "29-7459",
    "created_at": "2023-02-02T16:12:30.000000Z",
    "updated_at": "2023-02-02T16:12:30.000000Z"
  },
  {
    "id": 2,
    "name": "Jessika Howell",
    "email": "legros.raina@example.com",
    "employee_id": "29-6386",
    "created_at": "2023-02-02T16:12:30.000000Z",
    "updated_at": "2023-02-02T16:12:30.000000Z"
  }
]
```

>**GET /api/employees/<id>**
```bash
$ curl -s http://localhost:8000/api/employees/<id> -H "Authorization: Bearer <token>"
```
```json
  {
    "id": 2,
    "name": "Jessika Howell",
    "email": "legros.raina@example.com",
    "employee_id": "29-6386",
    "created_at": "2023-02-02T16:12:30.000000Z",
    "updated_at": "2023-02-02T16:12:30.000000Z"
  }
```

>**POST /api/employees** 
```sh
curl -X POST http://localhost:8000/api/employees \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo' \
  -d '{"name":"mike", "email":"mike@mike.com", "employee_id": "12-ABCD"}' 
```
```json
{
  "name": "mike",
  "email": "mike@test.com",
  "employee_id": "12-ABCD",
  "updated_at": "2023-02-02T20:51:46.000000Z",
  "created_at": "2023-02-02T20:51:46.000000Z",
  "id": 42
}
```
```sh
curl -X POST http://localhost:8000/api/employees \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo'
```

```json
{
  "errors": {
    "email": [
      "You must enter an email"
    ],
    "name": [
      "You Must Enter An Name"
    ],
    "employee_id": [
      "employee_id should be in format of 11-ABCDEFG"
    ]
  }
}
```


>**PUT /api/employees** 
```sh
curl -X PUT http://localhost:8000/api/employees/<id> \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo' \
  -d '{"name":"new name"}' 
```
```json
{
  "name": "new name",
  "email": "mike@test.com",
  "employee_id": "12-ABCD",
  "updated_at": "2023-02-02T20:51:46.000000Z",
  "created_at": "2023-02-02T20:51:46.000000Z",
  "id": 42
}
```

>**DELETE /api/employees/<id>** 
```sh
curl -X DELETE http://localhost:8000/api/employees/<id> \
  -H 'Content-Type: application/json' \
  -H 'Accept: application/json' \
  -H 'Authorization: Bearer 10|pmnpyKrbhcPfPhy15HiWSyn85Falty8bPXy3BbJo'
```
```json
{
  "message": "User With Id 42 Deleted",
}
```