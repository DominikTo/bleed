# bleed

bleed is a tool to test servers for the '[Heartbleed](http://heartbleed.com)' vulnerability ([CVE-2014-0160](https://cve.mitre.org/cgi-bin/cvename.cgi?name=CVE-2014-0160)).

## Usage

```
$ bleed example.org
> Connecting...
> Sending Client Hello
  Waiting for Server Hello...
< Received message: type = 22, ver = 0302, length = 61
< Received message: type = 22, ver = 0302, length = 6442
< Received message: type = 22, ver = 0302, length = 331
< Received message: type = 22, ver = 0302, length = 4
> Sending Heartbeat request
Unexpected EOF receiving record header. Server closed connection.
No heartbeat response. Server likely not vulnerable.
```

## Installation

### Prerequisites

* The package manager [Composer](http://getcomposer.org).
* Composer global vendor directory in your path (e.g. `export PATH=~/.composer/vendor/bin:$PATH`).
* You might have to add `"minimum-stability": "dev"` to your global `composer.json` (by default in `~/.composer/composer.json`).

### Actual installation

```
composer global require 'dominik/bleed=dev-master'
```

Composer will now install `bleed` and its dependencies.

## Updating

```
composer global update 'dominik/bleed'
```

## Fineprint

* [License](https://github.com/DominikTo/bleed/blob/master/LICENSE)
* [Disclaimer](http://knowyourmeme.com/memes/i-have-no-idea-what-im-doing)
