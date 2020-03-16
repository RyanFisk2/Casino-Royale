make:
	$(MAKE) -C TwoPlusTwoHandEvaluator/
	g++ -o casino-royale casino-royale.cpp

notable:
	g++ -o casino-royale casino-royale.cpp -I/usr/local/include/opencv4 -lopencv_core -lopencv_videoio -lopencv_objdetect -lwiringPi

clean:
	rm casino-royale
	rm TwoPlusTwoHandEvaluator/HandRanks.dat
