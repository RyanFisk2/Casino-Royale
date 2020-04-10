make full:
	$(MAKE) -C TwoPlusTwoHandEvaluator/
	g++ -o casino-royale casino-royale.cpp
 

make quick:
	g++ -o casino-royale casino-royale.cpp

make build:
	g++ -O2 -o casino-royale casino-royale.cpp

clean:
	rm casino-royale
	rm TwoPlusTwoHandEvaluator/HandRanks.dat
