# [Pezeshkbook](https://pezeshkbook.com)
# Labyrinth - Test Task for Senior Back-End Developer (PHP/Laravel) position

## Summary

We have a labyrinth which is a two-dimensional set of tiles which could
be either empty (meaning this is a passable block) or filled
(which means it's a wall).

Example of the labyrinth (black blocks are walls):

![example](./example.png)

The purpose of the task is to create a backend REST service which
provides the endpoints to create and solve these kind of labyrinths.

## Technical details

* use the Laravel framework
* persisting two data collections or tables (users, labyrinths)
* REST API should use BasicAuth
* user creation must work 'on-the-fly', no user deletion needed
* please provide a brief documentation with steps for setting up and
    describing the choice of technologies (e.g. DB or architecture)

## Endpoints

REST API should provide the following endpoints:

* return all the labyrinths for the current user:

Structure:

```
GET /labyrinth
```

Example:

```
GET /labyrinth HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* return a specific labyrinth of the user by id:

Structure:

```
GET /labyrinth/:id
```

Example:

```
GET /labyrinth/2 HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* create an empty labyrinth and returns the labyrinth id:

Structure:

```
POST /labyrinth
```

Example:

```
POST /labyrinth HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* set the type of the specific block of the labyrinth using x/y coordinates
    (type is either 'empty' or 'filled'):

Structure:

```
PUT /labyrinth/:id/playfield/:x/:y/:type
```

Example:

```
PUT /labyrinth/2/playfield/1/1/filled HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* set the starting block of the labyrinth using x/y coordinates:

Structure:

```
PUT /labyrinth/:id/start/:x/:y
```

Example:

```
PUT /labyrinth/2/start/1/3 HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* set the ending block of the labyrinth using x/y coordinates:

Structure:

```
PUT /labyrinth/:id/end/:x/:y
```

Example:

```
PUT /labyrinth/2/end/5/7 HTTP/1.1
Host: localhost
Authorization: Basic ...
```

* return a solution for the labyrinth:

Structure:

```
GET /labyrinth/:id/solution
```

Example:

```
GET /labyrinth/:id/solution HTTP/1.1
Host: localhost
Authorization: Basic ...
```

solution is the array of directions in the following format:

```
['left', 'up', 'right', 'down', 'down', 'down']
```
