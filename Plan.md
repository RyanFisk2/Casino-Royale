#Casino Royal Project Plan

*note to Gabe: we're not entirely sure yet if C or JavaScript is going to work best for what we want to make. Any guidance on 
thiswould be greatly appreciated, thank you!*

##Components:
1)
A standard deck of deck of cards with QR codes taped or glued to them. These will be used for input.

2)A QR code reader
The QR reader will scan the QR code that has been taped to the card. It will process this sticker and store the number and suit
of the card. This information will then be sent to the odds processing component.

3)Odds Calculation
This part will be a C(?) program that will take in the number and suit of both of a players cards and calculate their odds of 
winning a hand just based off of those two cards. Initially it will look for matching numbers or matching suit of the first two
cards, and calculate the odds of winning if the hand was just those two cards. Then for every card in the flop, turn, and river,
the odds will be updated based on what cards are there. For example, if the initial hand is 2H (2 of hearts) and KH(king of hearts),
the odds of winning will improve if any of the middle cards are a heart, or if one of them is a 2 or a king. To ensure accuracy, the hands will be categorized (i.e. pairs, full houses, straights, etc.)
with each type having a better chance of winning than the one before it (i.e. a pair of 2's beats a high card ace, three 2's beats
a pair of aces). Within each type, the highest card combination will have the best chance of winning. It will then send
the odds of winning and the cards in the players hand and the cards on the table (which will both update everytime a new card
is scanned), to the website interface.

4)Website Interface
This part of the project will allow multiple players who are not physically present at the table to play using the same deck
as the players on the table. The Odds calculation program will send the cards in the player's hand as well as their odds of 
winning to the website, which will display the cards the player has and the odds that they win. When a new card is scanned,
the website will update to include the cards on the table and the players new odds of winning based on those cards. 

##Timeline:
By checkpoint 1:
[ ] work out the ranking systems for odds calculator
[ ] pseudocode for odds calculator algorithms
[ ] Research JavaScript/C libraries for working with QR reader

By checkpoint 2:
[ ] QR reader sends card values to odds calculator, which processes and stores them
[ ] Basic Website interface, card input in the actual HTML/JavaScript files
[ ] QR codes made and assigned to each card and reading accurately
[ ] Website interface has some styling


##Interfaces:
Interface 1: Physical Card to QR Reader
In order to simplify the process of reading the card, we will have QR stickers on each card that contains the number and 
suit of that card. This will provide a nice, easy to use interface between the physical card and the QR reader. We will use a 
C(?) library to get the QR input into the format that we want it (i.e. 5D for 5 of diamonds)

Interface 2: QR Reader to Odds Calculator
The QR Reader will send the number and suit of the card to the odds calculator program through a raspberry Pi as a serial input
which will allow us to easily convert that input into the string or char array format that we want for the odds processing 
component.

Interface 3: Odds Calculator to Web Interface
Once the odds are calculated, the web interface will receive the cards in the players hand and their odds of winning from
the calculator program and display the user's cards and odds of winning to the website. Everytime a new card is scanned, the 
website will update with the new cards in the middle and the updated odds of winning. 

##Security (from "Securing the Internet of Things")
Since our app is game-based and probably won't have much sensitive information as currently designed, our biggest safety concern
is making sure that no players receive data that they shouldn't (i.e. the cards in another player's hand). In our design,
we decided that each player would have their own unique web interface, and therefore the only data they would share would
be the cards in the middle of the table. If we scaled the project up to allow betting, then we would need to have some kind
of encrypted communication between each player and the betting system so that no sensitive information could be stolen.
At the end of every game, the data of every player using the web interface will be erased to a) protect the privacy of
all players and b) to make sure no information remains vulnerable on the interface. The only connections allowed in this 
game will be between the web interface and the odds calculator, and the odds calculator to the QR reader. 

##Assignments
Ryan Fisk: Website Interface
Reese Jones: QR Reader
Niko Revliotis: Odds Calculator


