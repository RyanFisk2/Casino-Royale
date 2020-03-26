#include <iostream>
#include <string.h>
#include <zbar.h>
#include <opencv2/objdetect.hpp>
#include <opencv2/imgcodecs.hpp>
#include <opencv2/imgproc/imgproc.hpp>
#include <opencv2/videoio.hpp>
#include <wiringPi.h>
#include <time.h>

using namespace std;
using namespace cv;

// for HandRanks.dat
int HR[32487834];

// for cardNametoInt (prints out card value from given int, will keep in for QR testing)
const char cardNames[53][3] = { "??", // unknown card
	"2c", "2d", "2h", "2s", "3c", "3d", "3h", "3s",
	"4c", "4d", "4h", "4s", "5c", "5d", "5h", "5s",
	"6c", "6d", "6h", "6s", "7c", "7d", "7h", "7s",
	"8c", "8d", "8h", "8s", "9c", "9d", "9h", "9s",
	"tc", "td", "th", "ts", "jc", "jd", "jh", "js",
	"qc", "qd", "qh", "qs", "kc", "kd", "kh", "ks",
	"ac", "ad", "ah", "as" };

void printHand(int* pCards, int* cCards);
void printHand(int* hand);
int cardNametoInt(char* cardName);
int* combinePlyCommunity (int* pCards, int* cCards);
int lookupHand(int* pHand);
int lookupHand(int* pCards, int* cCards);
bool possibleCard(int card, int *pCards, int* cCards);
float calcOdds7(int score, int* pCards, int* cCards);
float calcOdds6(int* pCards, int* cCards);
float calcOdds5(int* pCards, int* cCards);
float avgScore6(int* pCards, int* cCards);
float avgScore5(int* pCards, int* cCards);
float calcOdds(int* pCards, int* cCards);
float calcAvgScore(int* pCards, int* cCards);
int* scanPlyCards(VideoCapture vid);
int* scanCommunityCards(VideoCapture vid, int* pCards);
VideoCapture initCamera(int width, int height, int frameRate);
bool cameraCheck(VideoCapture vid);

typedef struct
{
  string type;
  string data;
  vector <Point> location;
} decodedObject;


int main(int argc, char* argv[]) {
	// CHANGE TO THE PATH OF HandRanks.dat, will vary by system/OS
	char HandRanksLoc[] = "/home/pi/20_casino_royale/TwoPlusTwoHandEvaluator/HandRanks.dat";


	// Open HandRanks.dat and load it into memory
	memset(HR, 0, sizeof(HR));
	FILE * fin = fopen(HandRanksLoc, "rb");
	if(!fin) {
		printf("Failed to open HandRanks.dat\n");
		printf("Generate using Makefile in TwoPlusTwoHandEvaluator\n");
		return -1; // terminate program, we need table
	}
	size_t bytesread = fread(HR, sizeof(HR), 1, fin);
	fclose(fin);
	
	wiringPiSetup();
	pinMode(0, OUTPUT); // using WiringPi pins, type gpio readall to get all pins
	
	// NOW WE CAN BEGIN
	VideoCapture camera = initCamera(640, 480, 90); // best results with 1280x720, 1920x1080 can't push same framerate
	
	if(!cameraCheck(camera)) {
		printf("casino-royale: no camera detected\n");
		return -1;
	}
	
	int* pCards = nullptr;
	while(pCards == nullptr) {
		pCards = scanPlyCards(camera);
	}
	printf("Player Cards: %d, %d\n", pCards[0], pCards[1]);
	
	int* cCards = nullptr;
	while(cCards == nullptr) {
		cCards = scanCommunityCards(camera, pCards);
	}
	printf("Community Cards: %d, %d, %d, %d, %d\n", cCards[0], cCards[1], cCards[2], cCards[3], cCards[4], cCards[5]);
	printHand(pCards, cCards);

	printf("Score: %d / %d\n", lookupHand(pCards, cCards), lookupHand(combinePlyCommunity(pCards, cCards)));
	printf("Avg Possible Score: %f\n", calcAvgScore(pCards, cCards));
	printf("Odds: %f\n", calcOdds(pCards, cCards));


	//free the camera object
	camera.release();

	//free hands
	free(pCards);
	free(cCards);
}

bool cameraCheck(VideoCapture vid) {
	if(!(vid.isOpened())){
		//video failed to open, print error
		digitalWrite(0, HIGH);
		delay(500);
		digitalWrite(0, LOW);
		delay(100);
		digitalWrite(0, HIGH);
		delay(500);
		digitalWrite(0, LOW);
		delay(100);
		digitalWrite(0, HIGH);
		delay(500);
		digitalWrite(0, LOW);
		return false;
	}

	return true;
}

VideoCapture initCamera(int width, int height, int frameRate)
{
	VideoCapture vid(0);
	vid.set(CAP_PROP_FRAME_HEIGHT, height);
	vid.set(CAP_PROP_FRAME_WIDTH, width);
	vid.set(CAP_PROP_FPS, frameRate);

	return vid;
}

//scan each card when put on table
//return chars of the name and suit
// scan 2 cards (player cards)
int* scanPlyCards(VideoCapture vid)
{
	time_t startTime = time(0); // gets current system time

	Mat edges, output;
	Mat frame;
	Mat frameGray;
  	String data;
	int* pCards = (int*)malloc(sizeof(int) * 2);
	pCards[0] = 0;
	pCards[1] = 0;

	int cardsScanned = 0;

	// Create zbar scanner
	zbar::ImageScanner scanner;
		
	// Configure scanner
	scanner.set_config(zbar::ZBAR_NONE, zbar::ZBAR_CFG_ENABLE, 0); // dusables reading for all codes
	scanner.set_config(zbar::ZBAR_QRCODE, zbar::ZBAR_CFG_ENABLE, 1); // enables reading only QR codes
	

	// runs for 10 seconds
	while( (difftime(time(0), startTime) <= 10) && (cardsScanned < 2) ) // 10s
	{
		//poll frames from video feed until a QR code is read
		//vid >> frame;
		//sleep until new frame is available
		
		// NEW CODE FOR TESTING (EASIER TO UNDERSTAND)
		// Would replace the vid >> frame line
		// Using vid >> frame = vid.get() which doesn't process the image properly into a Mat frame / OutputArray
		// Attempting this might make QR reading a bit faster
		
		if(vid.read(frame) == false)
			continue;
		

		//std::string data = qrReader.detectAndDecode(frame, edges, output);
		


		// Convert image to grayscale
		cvtColor(frame, frameGray, cv::COLOR_BGR2GRAY);

		// Wrap image data in a zbar image
		zbar::Image image(frame.cols, frame.rows, "Y800", (uchar *)frameGray.data, frame.cols * frame.rows);

		// Scan the image for barcodes and QRCodes
		int scanResult = scanner.scan(image);

		//if(scanResult == -1)
		//	return NULL; // error from zbar

		if(scanResult <= 0)
			continue; // no symbols found or zbar error, onto the next frame

		//data = image.symbol_begin()->get_data();
  		// Print results
		for(zbar::Image::Image::SymbolIterator symbol = image.symbol_begin(); symbol != image.symbol_end(); ++symbol)
		{
			// HERE WE ARE ASSUMING THE QR CODE BEING SCANNED IS A NUMBER (NO OTHER QR CODE IS BEING SCANNED)
			data = symbol->get_data();
			int scannedCard = stoi(data);
			bool cardExists = false;

			// check if card has already been scanned
			for(int i = 0; i < 2; i++) {
				if(scannedCard == pCards[i])
					cardExists = true;
			}

			if(!cardExists) {
				printf("SCANNED: %d\n", scannedCard);
				pCards[cardsScanned] = scannedCard;
				cardsScanned++;
			}
		}
		
	}
	
	// scan failed, didn't get 2 cards
	if(cardsScanned != 2) {
		free(pCards);
		return nullptr;
	}
		
	
	digitalWrite(0, HIGH);
	delay(200);
	digitalWrite(0, LOW);
		
	return pCards;

}

//scan each card when put on table
//return chars of the name and suit
// scan 3-5 cards (community cards cards)
// community cards will be sorted to allow for fast duplication checking (log(n))
// required pCards to make sure there are no duplicates
int* scanCommunityCards(VideoCapture vid, int* pCards)
{
	time_t startTime = time(0); // gets current system time

	Mat edges, output;
	int count = 0;
	Mat frame;
	Mat frameGray;
  	String data;
	int* cCards = (int*)malloc(sizeof(int) * 5);
	
	for(int i = 0; i < 5; i++) {
		cCards[i] = 0;
	}

	int cardsScanned = 0;

	// Create zbar scanner
	zbar::ImageScanner scanner;
		
	// Configure scanner
	scanner.set_config(zbar::ZBAR_NONE, zbar::ZBAR_CFG_ENABLE, 0); // dusables reading for all codes
	scanner.set_config(zbar::ZBAR_QRCODE, zbar::ZBAR_CFG_ENABLE, 1); // enables reading only QR codes
	

	// runs for 10 seconds
	while( (difftime(time(0), startTime) <= 10) && (cardsScanned < 5) ) // ~10s
	{
		//poll frames from video feed until a QR code is read
		//vid >> frame;
		//sleep until new frame is available
		
		// NEW CODE FOR TESTING (EASIER TO UNDERSTAND)
		// Would replace the vid >> frame line
		// Using vid >> frame = vid.get() which doesn't process the image properly into a Mat frame / OutputArray
		// Attempting this might make QR reading a bit faster
		
		if(vid.read(frame) == false)
			continue;
		

		//std::string data = qrReader.detectAndDecode(frame, edges, output);
		


		// Convert image to grayscale
		cvtColor(frame, frameGray, cv::COLOR_BGR2GRAY);

		// Wrap image data in a zbar image
		zbar::Image image(frame.cols, frame.rows, "Y800", (uchar *)frameGray.data, frame.cols * frame.rows);

		// Scan the image for barcodes and QRCodes
		int scanResult = scanner.scan(image);

		//if(scanResult == -1)
		//	return NULL; // error from zbar

		if(scanResult <= 0)
			continue; // no symbols found or zbar error, onto the next frame

		//data = image.symbol_begin()->get_data();
  		// Print results
		for(zbar::Image::Image::SymbolIterator symbol = image.symbol_begin(); symbol != image.symbol_end(); ++symbol)
		{
			// HERE WE ARE ASSUMING THE QR CODE BEING SCANNED IS A NUMBER (NO OTHER QR CODE IS BEING SCANNED)
			data = symbol->get_data();
			int scannedCard = stoi(data);
			bool cardExists = false;

			// check if card has already been scanned in cCards
			for(int i = 0; i < 5; i++) {
				if(scannedCard == cCards[i])
					cardExists = true;
			}

			// for pCards/plyCards
			for(int i = 0; i < 2; i++) {
				if(scannedCard == pCards[i])
					cardExists = true;
			}



			if(!cardExists) {
				printf("SCANNED: %d\n", scannedCard);
				cCards[cardsScanned] = scannedCard;
				cardsScanned++;
			}
		}
		
	}
	
	// scan failed, didn't get 2 cards
	if(cardsScanned < 3) {
		free(cCards);
		return nullptr;
	}
	
	digitalWrite(0, HIGH);
	delay(200);
	digitalWrite(0, LOW);

	return cCards;

}

// checks size of cCards and returns from proper odds function
float calcOdds(int* pCards, int* cCards) {
	int cCardsCount = 0;
	while(cCardsCount < 5 && cCards[cCardsCount] != 0) {
		cCardsCount++;
	}

	printf("%d cards detected\n", cCardsCount);

	if(cCardsCount == 3)
		return calcOdds5(pCards, cCards);
	else if(cCardsCount == 4)
		return calcOdds6(pCards, cCards);
	else if(cCardsCount == 5)
		return calcOdds7(lookupHand(pCards, cCards), pCards, cCards);
	else
		return -1;

}



float calcAvgScore(int* pCards, int* cCards) {
	int cCardsCount = 0;
	while(cCardsCount < 5 && cCards[cCardsCount] != 0) {
		cCardsCount++;
	}
	
	printf("%d cards detected\n", cCardsCount);
	
	if(cCardsCount == 3)
		return avgScore5(pCards, cCards);
	else if(cCardsCount == 4)
		return avgScore6(pCards, cCards);
	else if(cCardsCount == 5)
		return lookupHand(pCards, cCards);
	else
		return -1;
}


// Prints the player hand in the following format:
// (plyCard1 plyCard2) communityCard1 communityCard2...
// Unknown communityCards are printed as ??
//
// Parameters
//	int* pCards - Array of player cards (values/ints), length must equal 2
//	int* cCards - Array of community cards (value/ints), length must equal 5 (unknown cards be 0)
void printHand(int* pCards, int* cCards) {
	
	// player cards
	printf("Player Hand: (%s %s) ", cardNames[pCards[0]], cardNames[pCards[1]]);
	
	// community cards
	for(int i = 0; i < 5; i++) {
		printf("%s ", cardNames[cCards[i]]);
	}
	printf("\n");
}

// Prints the player hand in the following format:
// (plyCard1 plyCard2) communityCard1 communityCard2...
// Unknown communityCards are printed as ??
//
// Parameters
//	int* hand - Array of player cards and community cards, length must be 7 (unknown cards be 0), player cards must be idx 0 and 1
void printHand(int* hand) {
	printf("Player Hand: (%s %s) %s %s %s %s %s\n", cardNames[hand[0]], cardNames[hand[1]], cardNames[hand[2]], cardNames[hand[3]], cardNames[hand[4]], cardNames[hand[5]], cardNames[hand[6]]);
}


// Combines player and community cards into a single array called the hand
//
// Parameters
//	int* pCards - Array of player cards (must be size 2)
//	int* cCards - Array of community cards (must be size 5), unknown cards should be 0
// Returns
//	int* hand - Array of size 7 with pCards at idx 0 and 1, cCards for 3-6
int* combinePlyCommunity (int* pCards, int* cCards) {

	// ply, ply, com, com, com, com, com
	int* pokerHand = (int*)malloc(sizeof(int) * 7);
	memset(pokerHand, 0, sizeof(pokerHand));

	pokerHand[0] = pCards[0];
	pokerHand[1] = pCards[1];


	// add community cards
	for(int i = 0; i <= 4; i++) {
		pokerHand[i+2] = cCards[i];
	}

	return pokerHand;

}

// Returns integer value of given cardName (<value><suit>) (Ex: 2c = 2 of clubs, th = 10 of hearts, qs = queen of spades)
// Parameters
//	char* cardName - Card name in the format specificed above, must be size 2
// Returns
//	int cardVal - Integer card value for the given cardName
int cardNametoInt(char* cardName) {
	
	for(int i = 0; i <= 52; i++) {
		if(strcmp(cardNames[i], cardName) == 0)	
			return i;
	}

	return 0;
}

// Looks up and returns the score of a 5/6/7-card hand in HandRanks.dat
// Returns only the current score, not projected for 5 and 6 card
// Slightly modified version of TwoPlusTwoHandEvaluator code
//
// Parameters
//	int* hand - Array of player cards and community cards, length must be 7 (unknown cards be 0), player cards must be idx 0 and 1
// Returns
//	int score - Computed score found in HandRanks.dat for a given hand (current score)
int lookupHand(int* pHand) {
	
	int p = HR[53 + *pHand++];
	p = HR[p + *pHand++];
	p = HR[p + *pHand++];
	p = HR[p + *pHand++];
	p = HR[p + *pHand++];
	if(*pHand == 0) { // pass 0 only once (5 card/flop analysis)
		return HR[p + *pHand++];
	}
	else { // 6 or 7 card, pass 0 once or the river
		p = HR[p + *pHand++];
		return HR[p + *pHand++];
	}
}

// Looks up and returns the score of a 5/6/7-card hand in HandRanks.dat
// Returns only the current score, not projected for 5 and 6 card
// Slightly modified version of TwoPlusTwoHandEvaluator code
//
// Parameters
// 	int* pCards - Array of playerCards, must be of length 2
// 	int* cCards - Array of communityCards, must be of length 5, unknown cards as 0
// Returns
//
//	int score - Computed score found in HandRanks.dat for a given hand (current score)
int lookupHand(int* pCards, int* cCards) {
	
	int p = HR[53 + pCards[0]];
	p = HR[p + pCards[1]];
	p = HR[p + cCards[0]];
	p = HR[p + cCards[1]];
	p = HR[p + cCards[2]];
	if(cCards[3] == 0) { // pass 0 only once (5 card/flop analysis)
		return HR[p + cCards[3]];
	}
	else { // 6 or 7 card, pass 0 once or the river
		p = HR[p + cCards[3]];
		return HR[p + cCards[4]];
	}
}

// Simply returns false if the card exists on the table or in your hand
//
// Parameters
//	int card - Card value to check whether it exists in pCards or cCards
//	int* pCards - Array of player cards to search through
//	int* cCards - Array of community cards to search through
// Returns
//	bool possibleCard - Returns true if the card doesn't exist in pCards or cCards, otherwise false
bool possibleCard(int card, int* pCards, int* cCards) {
	if( (card == pCards[0]) || (card == pCards[1]) || (card == cCards[0]) || (card == cCards[1]) || (card == cCards[2]) || (card == cCards[3]) || (card == cCards[4]) || (card == cCards[5]) )
		return false;

	return true;
}

// Calculates the % chance of winning by computing the quantile your hand is at versus all possible opponent hands
// Parameters
//	int score - score to compare to
//	int* pCards - Array of player cards
//	int* cCards - Array of community cards
// Returns
//	float odds - % chance of winning the hand / quantile of hand strength
float calcOdds7(int score, int* pCards, int* cCards) {
	int opponentCards[2] = {0, 0};

	int worseHands = 0;
	int betterHands = 0;

	// This loop checks the score of all possible combinations of opponent
	// cards given the 5 community cards, and computers the number of hands
	// better and worse than the passed in score
	for(int i = 1; i <= 52; i++) { // gets first card
		if(!possibleCard(i, pCards, cCards))	
			continue;
		
		for(int j = i+1; j <= 52; j++) { // gets second card
			if(!possibleCard(j, pCards, cCards))	
				continue;
			
			opponentCards[0] = i;
			opponentCards[1] = j;
		
			int opponentScore = lookupHand(opponentCards, cCards);

			if(opponentScore > score) {
				betterHands++;
			}
			else {
				worseHands++;
			}
		}
	}

	return (float)( (float)worseHands / (float)(worseHands + betterHands) );

}


// Calculates the % chance of winning by computing the quantile your hand is at versus all possible opponent hands
// With 6 hand, we add an additional layer (for loop) to compare scores with all possible rivers
// Parameters
//	int* pCards - Array of player cards
//	int* cCards - Array of community cards
// Returns
//	float odds - % chance of winning the hand / quantile of hand strength
float calcOdds6(int* pCards, int* cCards) {
	int opponentCards[2] = {0, 0};

	int worseHands = 0;
	int betterHands = 0;

	// This loop checks the score of all possible combinations of opponent
	// cards given the 5 community cards, and computers the number of hands
	// better and worse than the passed in score
	for(int i = 1; i <= 52; i++) { // gets first card
		if(!possibleCard(i, pCards, cCards))	
			continue;
		
		for(int j = i+1; j <= 52; j++) { // gets second card
			if(!possibleCard(j, pCards, cCards))
				continue;
			
			opponentCards[0] = i;
			opponentCards[1] = j;

			for(int k = 1; k <= 52; k++) {
				if( (!possibleCard(k, opponentCards, cCards)) || (!possibleCard(k, pCards, cCards)) ) {
					continue;
				}

				cCards[4] = k;
				int opponentScore = lookupHand(opponentCards, cCards);
				int playerScore = lookupHand(pCards, cCards);

				if(opponentScore > playerScore) {
					betterHands++;
				}
				else {
					worseHands++;
				}
				
				cCards[4] = 0; // reset altered card

			}
		

		}
	}


	return (float)( (float)worseHands / (float)(worseHands + betterHands) );

}

// Calculates the % chance of winning by computing the quantile your hand is at versus all possible opponent hands
// With 5 hand, we add 2 additional layers (for loops) to compare scores with all possible turns and rivers
// Parameters
//	int* pCards - Array of player cards
//	int* cCards - Array of community cards
// Returns
//	float odds - % chance of winning the hand / quantile of hand strength
float calcOdds5(int* pCards, int* cCards) {

	int opponentCards[2] = {0, 0};

	int worseHands = 0;
	int betterHands = 0;

	// This loop checks the score of all possible combinations of opponent
	// cards given the 5 community cards, and computers the number of hands
	// better and worse than the passed in score
	for(int i = 1; i <= 52; i++) { // gets first card
		if(!possibleCard(i, pCards, cCards))	
			continue;
		
		for(int j = i+1; j <= 52; j++) { // gets second card
			if(!possibleCard(j, pCards, cCards))	
				continue;
			
			opponentCards[0] = i;
			opponentCards[1] = j;

			for(int k = 1; k <= 52; k++) {
				if( (!possibleCard(k, opponentCards, cCards)) || (!possibleCard(k, pCards, cCards)) ) {
					continue;
				}


				for(int v = k+1; v <= 52; v++) {
					
					if( (!possibleCard(v, opponentCards, cCards)) || (!possibleCard(v, pCards, cCards)) ) {
						continue;
					}
				
					cCards[3] = k;	
					cCards[4] = v;

					int opponentScore = lookupHand(opponentCards, cCards);
					int playerScore = lookupHand(pCards, cCards);
			
					if(opponentScore > playerScore) {
						betterHands++;
					}
					else {
						worseHands++;
					}

					cCards[3] = 0;
					cCards[4] = 0; // reset
	
				}

			
			}
		

		}
	}

	return (float)( (float)worseHands / (float)(worseHands + betterHands) );

}


// Computes average possible score of a hand given 6 cards by taking average score of all possible river outcomes
// Parameters
//	int* pCards - Array of player cards (must be size 2)
//	int* cCards - Array of community cards (must be size 5), cCards[4] is ignored
// Returns
//	float avgScore - Average score possible on all possible rivers
float avgScore6(int* pCards, int* cCards) {

	float avgScore = 0;
	int count = 0;
	// this loop will get every possible card (of the remaining 52 - 6 = 46) and compute an average score
	for(int i = 1; i <= 52; i++) {
		if(!possibleCard(i, pCards, cCards)) {
			continue; // card is already on the table
		}

		cCards[4] = i;
		avgScore += lookupHand(pCards, cCards);
		count++;
	
		cCards[4] = 0; // reset altered value	
	
	}

	return (float)avgScore / (float)count; // dividing by 46 since thats the remaining number of cards to check
}

// Computes average possible score of a hand given 5 cards by taking average score of all possible turn and river outcomes
// Parameters
//	int* pCards - Array of player cards (must be size 2)
//	int* cCards - Array of community cards (must be size 5), cCards[3] and cCards[4] is ignored
// Returns
//	float avgScore - Average score possible on all possible turns and rivers
float avgScore5(int* pCards, int* cCards) {

	printf("AVG SCORE 5\n");
	float avgScore = 0;
	int count = 0;
	// this loop will get every possible card (of the remaining 52 - 6 = 46) and compute an average score
	for(int i = 1; i <= 52; i++) {
		if(!possibleCard(i, pCards, cCards)) {
			continue; // card is already on the table
		}

		for(int j = i+1; j <= 52; j++) {
			if(!possibleCard(j, pCards, cCards))
				continue;

			cCards[3] = i;
			cCards[4] = j;

			avgScore += lookupHand(pCards, cCards);
			count++;
		
			cCards[3] = 0;
			cCards[4] = 0;
		}
	
	}

	return (float)avgScore / (float)count;
}


