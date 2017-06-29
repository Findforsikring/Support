# Findforsikring/Support

## Installation
Add the private repository to composer.json:
```
"repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:findforsikring/support.git"
        }
    ],
```
Add the package to require:
```
        "findforsikring/support": "dev-master"
```
Note: If your SSH key is not whitelisted at GitHub, composer will ask you to generate a token the first time the package is installed.

## Examples
```
// Send an SMS
sms("28291000", "Morten", "Davs chef!");

// Get Danish holidays for a year
$holidays = danish_holidays(2017);
Returns:
{
   Nytårsdag: "2017-01-01",
   Palmesøndag: "2017-04-09",
   Skærtorsdag: "2017-04-13",
   Langfredag: "2017-04-14",
   Påskedag: "2017-04-16",
   2. påskedag: "2017-04-17",
   Store bededag: "2017-05-12",
   Kristi himmelfart: "2017-05-25",
   Pinsedag: "2017-06-04",
   2. pinsedag: "2017-06-05",
   Juleaften: "2017-12-24",
   Juledag: "2017-12-25",
   2. juledag: "2017-12-26",
   Nytårsaften: "2017-12-31"
}

// Convert a number to Danish Kroner
echo toDKK(1897.75); // => "kr. 1.897,75"
```
## Documentation
Check `/docs` folder for detailed documentation