# Scrabble

## Rules

* Support for two players
* Each players has 7 tiles with letters from A-Z
* Words should be at least 2 letters
* All words on the board considered valid
* Words can be placed vertically or horizontally
* The dimension of the board is 15×15 tiles
* You get 1 point for every tile that you place at the board
* You pick a new tile from the bag for every tile that put at the board
* The game ends when the bag is put of tiles

## REST API

### Status (`GET`)

Returns

```
{
	score,
	users,
	current,
	tiles,
	board
}
```

### Game (`POST`)

Expects

```
{
	uid,
	word,
	orientation,
	x,
	y
}
```

Returns

```
{
	errors
}
```

### Start (`DELETE`)

Returns

```
{}
```

## To be improved

* Allow more players
* Dictionary that can be used to validate words
