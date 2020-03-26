make:
	$(MAKE) -C TwoPlusTwoHandEvaluator/
	g++ -o casino-royale casino-royale.cpp -I/usr/local/include/opencv4 -lopencv_core -lopencv_videoio -lopencv_objdetect -lopencv_imgproc -lzbar -lwiringPi
 

notable:
	g++ -o casino-royale casino-royale.cpp -I/usr/local/include/opencv4 -lopencv_core -lopencv_videoio -lopencv_objdetect -lopencv_imgproc -lzbar -lwiringPi

clean:
	rm casino-royale
	rm TwoPlusTwoHandEvaluator/HandRanks.dat
