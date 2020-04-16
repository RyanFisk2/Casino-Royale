make full:
	$(MAKE) -C TwoPlusTwoHandEvaluator/
	g++ -o casino-royale casino-royale.cpp
 

make quick:
	g++ -o casino-royale casino-royale.cpp
	
make quick threaded:
	g++ -pthread -std=c++11 -o casino-royale casino-royale-new.cpp

make build:
	g++ -O2 -o casino-royale casino-royale.cpp

clean:
	rm casino-royale
	rm TwoPlusTwoHandEvaluator/HandRanks.dat
