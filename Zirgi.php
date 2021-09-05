<?php

class Runner
{
    public string $name;
    public int $trackPosition = 0;
    public int $time = 0;
    public int $finishTime = 0;
    public int $betKoef ;
    public int $finishPosition = 0;

    function __construct($nameSymbols)
    {
        $this->name = $nameSymbols[array_rand($nameSymbols)];
        $this->betKoef = rand(1,5);
    }
}


class RunSimulator
{
    public int $runnerCount;
    private int $trackLength;
    public array $track;
    public array $runners;
    private array $nameSymbols = ['@', '#', '$', '%', '&', '*', '+', '^', '!'];
    public array $winners = [];

    function __construct(int $runnerCount, int $trackLength)
    {
        $this->runnerCount = $runnerCount;
        $this->trackLength = $trackLength;
        $this->makeRunners();
        $this->makeTrack();
    }

    private function makeRunners()
    {
        for ($i = 0; $i < $this->runnerCount; $i++) {
            $this->runners[] = new Runner($this->nameSymbols);
            $this->nameSymbols = array_diff($this->nameSymbols, [$this->runners[$i]->name]);
        }
    }

    private function makeTrack()
    {
        foreach (range(1, $this->trackLength) as $i) {
            $this->track[] = '_';
        }
    }

    public function runSection()
    {
        foreach ($this->runners as $runner) {
            $runner->trackPosition += rand(1, 2);
            $runner->time++;
        }
    }

    public function updateWinners()
    {
        foreach ($this->runners as $runner) {
            if ($runner->trackPosition >= $this->trackLength && !in_array($runner, $this->winners)) {
                $runner->finishTime = $runner->time;
                $this->winners[] = $runner;
            }
        }
    }
}

class SimulatorInterface
{
    private RunSimulator $simulator;
    private array $bets = [];

    function __construct(RunSimulator $simulator)
    {
        $this->simulator = $simulator;
    }

    private function displaySection()
    {
        foreach ($this->simulator->runners as $runner) {
            foreach ($this->simulator->track as $key => $section) {
                echo $key === $runner->trackPosition ? $runner->name : $section;
            }
            echo PHP_EOL;
        }
    }

    public function displayRun()
    {
        while (count($this->simulator->winners) < $this->simulator->runnerCount) {
            system('clear');
            $this->displaySection();
            usleep(250000);
            $this->simulator->runSection();
            $this->simulator->updateWinners();
        }
        system('clear');
        $this->displayWinners();
    }

    private function displayWinners()
    {
        $position = 0;
        $previousRunnerTime = 0;
        foreach ($this->simulator->winners as $winner) {
            if ($winner->finishTime !== $previousRunnerTime) {
                $position++;
            }
            $winner->finishPosition = $position;
            echo "Position $position  is $winner->name " . PHP_EOL;
            $previousRunnerTime = $winner->finishTime;
        }
    }
    public function displayBetWins()
    {
        $totalWon = 0;
        $firstPositionHorses = [];
        foreach ($this->simulator->runners as $horse)
        {
            if ($horse->finishPosition == 1){
                $firstPositionHorses [] = $horse;
            }
        }
        foreach ($firstPositionHorses as $horse)
        {
            if(array_key_exists($horse->name,$this->bets))
            {

                $totalWon += $this->bets[$horse->name]*$horse->betKoef;
            }
        }
//        echo "Your horses finished :".PHP_EOL;
//        foreach ($this->bets as $horse=>$horseBet){
//            echo "$horse finished -> ".$this->simulator->runners[$horse].PHP_EOL;
//        }
        echo 'You won betting '.$totalWon.'$'.PHP_EOL;

    }
    public function displayHorses()
    {
        echo 'Today running : '.PHP_EOL;
        foreach ($this->simulator->runners as $horse)#
        {
            echo "Horse --> $horse->name with bet coefficent : $horse->betKoef".PHP_EOL;
        }
    }
    public function PutBets()
    {
        $more = true;
        while ($more) {
            $chosenHorse = readline('Please enter horse symbol to put bet on : ');
            $bet = readline('Please enter amount to bet on Horse : ');
            $bet = (int)$bet;
            $this->bets[$chosenHorse] = $bet;
            if (readline("Enter 'more' to put another bet or 'start' to begin race : ") === 'more')
            {
                continue;
            }
            $more = false;


        }
    }
}

$displayRun = new SimulatorInterface(new RunSimulator(5, 42));
$displayRun->displayHorses();
$displayRun->PutBets();
$displayRun->displayRun();
$displayRun->displayBetWins();









