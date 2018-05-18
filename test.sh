#!/bin/bash
cat shitRandom.bin |rngtest -c 50000 > rngtest-shitRandom.bin.report.txt 2>&1
cat shitRandomFiltered.bin |rngtest -c 50000 > rngtest-shitRandomFiltered.bin.report.txt 2>&1
cat weakRandom.bin |rngtest -c 50000 > rngtest-weakRandom.bin.report.txt 2>&1
cat weakRandomFiltered.bin |rngtest -c 50000 > rngtest-weakRandomFiltered.bin.report.txt 2>&1
cat goodRandom.bin |rngtest -c 50000 > rngtest-goodRandom.bin.report.txt 2>&1
cat goodRandomFiltered.bin |rngtest -c 50000 > rngtest-goodRandomFiltered.bin.report.txt 2>&1

dieharder -a -g 201 -f shitRandom.bin > dieharder-shitRandom.bin.report.txt
dieharder -a -g 201 -f shitRandomFiltered.bin > dieharder-shitRandomFiltered.bin.report.txt
dieharder -a -g 201 -f weakRandom.bin > dieharder-weakRandom.bin.report.txt
dieharder -a -g 201 -f weakRandomFiltered.bin > dieharder-weakRandomFiltered.bin.report.txt
dieharder -a -g 201 -f goodRandom.bin > dieharder-goodRandom.bin.report.txt
dieharder -a -g 201 -f goodRandomFiltered.bin > dieharder-goodRandomFiltered.bin.report.txt
