# 20_casino_royale

## Prerequisites
- OpenCV ([installation instructions](https://docs.opencv.org/4.2.0/d7/d9f/tutorial_linux_install.html))
- ZBar ([installation instructions](https://www.learnopencv.com/barcode-and-qr-code-scanner-using-zbar-and-opencv/))
- WiringPi ([installation instructions](http://wiringpi.com/download-and-install/))
	- Only required if utilizing a piezzo buzzer/speaker when scanning QR codes
	- Make sure you're using the correct version for your RPi (requires 2.52 for RPi 4)
- LibCurl Dev (sudo apt-get install libcurl4-openssl-dev)

## Operation Instructions
1. Edit the following line within the main function in casino-royale.cpp: `char HandRanksLoc[] = "/home/pi/20_casino_royale/TwoPlusTwoHandEvaluator/HandRanks.dat";`
	- Simply substitute the section "/home/pi/" section with the directory the repo is stored in (starting from root)
2. Run the Makefile in the root of the repository ("make" on command-line)
	- HandRanks.dat isn't tracked as it is >100MB
	- A 2011 i7 Macbook Pro takes ~30s to generate the table
	- A Raspberry Pi 4 takes ~100s to generate teh table

### Credits
[TwoPlusTwoEvaluator](https://github.com/tangentforks/TwoPlusTwoHandEvaluator/tree/6b75c85060fd78d3a12d3da04fc3f8e29f65af12): Allowed for the odds calculations to be possible
