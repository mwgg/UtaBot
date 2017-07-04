UtaBot
======
Simple IRC bot, written in PHP, extensible with plugins.
### Usage
UtaBot is launched from the command line with supplied parameters.
```
php utabot.php -s irc.server.net -p 6667 -n UtaBot -r Bot -c "#chan1 #chan2" -a passwd
```
Supplying `-h` brings up the help message.
```
-s <server>      IRC server name or IP address
-p <port>        [Optional] Port to connect to, defaults to 6667
-n <nickname>    Nickname to use
-r <realname>    [Optional] Real name to use. If not specified, nickname value will be used
-c '<channels>'  IRC channels enclosed in double or single quotes, separated by spaces
-a <password>    [Optional] Administration password (required for bot related features like changing nicknames)
```
Upon executing utabot.php you will also be prompted for an optional authentication message to be passed to the server, such as `QUOTE PASS password`, `NICKSERV identify password`, or whatever the case may be.

Airports.php plugin requires extra files (maintained by me in other repos), which are noted below. Please supply them in the appropriate directories to enable those commands.
### Commands
Certain commands, like changing bot nickname or requesting the bot to join other channels require Bot administrator password to be supplied. This password is set at launch using the `-a` parameter.
Commands issued from a channel would produce a response on said channel. Likewise, PM commands will be responded to with PM.
#### Core
* `!help` brings up the defined help message listing available commands
* `!say <#chan> <message>` sends the message to a particular channel (the bot must be on the channel)
* `!server <passwd> <message>` sends a raw message to the server, to perform administrative or other actions otherwise not available by standard commands. Administrator password must be supplied
* `!quit <passwd>` disconnects UtaBot from the server
* `!join <passwd> <#channel> [#channel ...]` requests the bot to attempt joining the specified channels
* `!part <passwd> <#channel> [#channel ...]` requests the bot to leave the specified channels
* `!nick <passwd> <nickname>` changes bot's nickname
* `!uptime` shows bot uptime

#### Other commands
* `!lebowski` or `!l` will produce a random quote from The Big Lebowski (explicit content)
* `!carlin` or `!c` will produce a random quote by George Carlin (explicit content)
* `!metar <ICAO> [ICAO ...]` displays aviation weather reports (METARs) for the supplied 4-letter airport ICAO codes
* `!taf <ICAO> [ICAO ...]` displays aviation weather forecasts (TAFs)
* `!vatsim` loads and displays number of online pilots and ATC positions on the VATSIM network
* `!greatcircle <ICAO> <ICAO>` or `!gc <ICAO> <ICAO>` produces great circle distance between two airports defined by ICAO codes (requires [mwgg/Airports](https://github.com/mwgg/Airports) and [mwgg/GreatCircle](https://github.com/mwgg/GreatCircle))
* `!airport <ICAO>` displays airport information, such as name, city, country, coordinates, elevation, local time, sunrise, sunset (requires [mwgg/Airports](https://github.com/mwgg/Airports))

Apart from the commands above, all messages are analyzed to find URLs and fetch the respective pages' titles and display them on the channel.

### Plugin guidelines
Plugins contain sets of functions to be executable as commands, and are defined in *.php files inside plugins subdirectory and are automatically loaded at execution.

Each plugin must be a PHP class extending the class `Actions`, containing methods with lowercase names corresponding to commands to be called by users. Each of these methods will be called any time a command with the same name has been issued, and shall accept three arguments: `String $channel, String $nickname, Array $arguments` (name of the channel, nickname of the user issuing the command, and an array of everything that has been supplied after the command, separated by spaces.

Return value can be a string or an array. Array elements will be returned back to the channel/user as multiple lines.

If the returned value should be sent to a different channel, than the command came from, the return value must be an array and must contain an element `chan` with the name of the channel.

If the response should instead go to the server directly, the array return must contain an element `server` with any value.

Misc.php contains a method `analyze`, which checks every message received from active channels. At the moment, this method checks if a message contains URLs and loads their page titles, and may be extended to check for other things and append responses to the `$result` array.
