make:
	$(MAKE) -C TwoPlusTwoHandEvaluator/
	g++ -o casino-royale casino-royale.cpp

notable:
	g++ -o casino-royale casino-royale.cpp

clean:
	rm casino-royale
	rm TwoPlusTwoHandEvaluator/HandRanks.dat
