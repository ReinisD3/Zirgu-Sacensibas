<?php

class Runner
{
    private string $name;

    function __construct($nameSymbols)
    {
        $this->name = $nameSymbols[array_rand($nameSymbols)];
    }
    public function getName(): string
    {
        return $this->name;
    }
}
class HorseRaceInfo
{
    private Runner $runner;
    private int $trackPosition = 0;
    private int $time = 0;
    private int $finishTime = 0;
    private int $betKoef ;
    private int $finishPosition = 0;

    public function __construct(Runner $runner)
    {
        $this->runner = $runner;
        $this->betKoef = rand(1,5);
    }
    public function setFinishPosition(int $finishPosition): void
    {
        $this->finishPosition = $finishPosition;
    }
    public function getFinishPosition(): int
    {
        return $this->finishPosition;
    }
    public function getBetKoef(): int
    {
        return $this->betKoef;
    }
    public function getTime(): int
    {
        return $this->time;
    }
    public function addTime(int $time): void
    {
        $this->time += $time;
    }
    public function getTrackPosition(): int
    {
        return $this->trackPosition;
    }
    public function setTrackPosition(int $trackPosition): void
    {
        $this->trackPosition += $trackPosition;
    }
    public function getFinishTime(): int
    {
        return $this->finishTime;
    }
    public function setFinishTime(int $finishTime): void
    {
        $this->finishTime = $finishTime;
    }
    public function getRunner(): string
    {
        return $this->runner->getName();
    }

}
class RunSimulator
{
    private int $runnerCount;
    private int $trackLength;
    private array $track;
    private array $runners;
    private array $nameSymbols = ['@', '#', '$', '%', '&', '*', '+', '^', '!'];
    public array $winners = [];
    private array $horsesRaceInfo;

    function __construct(int $runnerCount, int $trackLength)
    {
        $this->runnerCount = $runnerCount;
        $this->trackLength = $trackLength;
        $this->makeRunners();
        $this->makeTrack();
        $this->makeHorsesRaceInfo();
    }
    private function makeHorsesRaceInfo()
    {
        foreach ($this->runners as $runner)
        {
            $this->horsesRaceInfo[] = new HorseRaceInfo($runner);
        }
    }
    private function makeRunners():void
    {
        for ($i = 0; $i < $this->runnerCount; $i++) {
            $this->runners[] = new Runner($this->nameSymbols);
            $this->nameSymbols = array_diff($this->nameSymbols, [$this->runners[$i]->getName()]);
        }
    }
    private function makeTrack():void
    {
        foreach (range(1, $this->trackLength) as $i) {
            $this->track[] = '_';
        }
    }
    public function runSection():void
    {
        foreach ($this->horsesRaceInfo as $horse) {
            $horse->setTrackPosition(rand(1, 2));
            $horse->addTime(1);
        }
    }
    public function updateWinners():void
    {
        foreach ($this->horsesRaceInfo as $horse) {
            if ($horse->getTrackPosition() >= $this->trackLength && !in_array($horse, $this->winners)) {
                $horse->setFinishTime($horse->getTime());
                $this->winners[] = $horse;
            }
        }
    }
    public function getRunnerCount(): int
    {
        return $this->runnerCount;
    }
    public function getHorsesRaceInfo(): array
    {
        return $this->horsesRaceInfo;
    }
    public function getTrack(): array
    {
        return $this->track;
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
    private function displaySection():void
    {
        foreach ($this->simulator->getHorsesRaceInfo() as $horse) {
            foreach ($this->simulator->getTrack() as $key => $section) {
                echo $key === $horse->getTrackPosition() ? $horse->getRunner() : $section;
            }
            echo PHP_EOL;
        }
    }
    public function displayRun():void
    {
        while (count($this->simulator->winners) < $this->simulator->getRunnerCount()) {
            system('clear');
            $this->displaySection();
            usleep(250000);
            $this->simulator->runSection();
            $this->simulator->updateWinners();
        }
        system('clear');
        $this->displayWinners();
    }
    private function displayWinners():void
    {
        $position = 0;
        $previousRunnerTime = 0;
        foreach ($this->simulator->winners as $winner) {
            if ($winner->getFinishTime() !== $previousRunnerTime) {
                $position++;
            }
            $winner->setFinishPosition($position);
            echo "Position $position  is ".$winner->getRunner()  . PHP_EOL;
            $previousRunnerTime = $winner->getFinishTime();
        }
    }
    public function displayBetWins():void
    {
        $totalWon = 0;
        $firstPositionHorses = [];
        foreach ($this->simulator->getHorsesRaceInfo() as $horse)
        {
            if ($horse->getFinishPosition() == 1){
                $firstPositionHorses [] = $horse;
            }
        }
        foreach ($firstPositionHorses as $horse)
        {
            if(array_key_exists($horse->getRunner(),$this->bets))
            {

                $totalWon += $this->bets[$horse->getRunner()]*$horse->getBetKoef();
            }
        }
//        echo "Your horses finished :".PHP_EOL;
//        foreach ($this->bets as $horse=>$horseBet){
//            echo "$horse finished -> ".$this->simulator->runners[$horse].PHP_EOL;
//        }
        echo 'You won betting '.$totalWon.'$'.PHP_EOL;
    }
    public function displayHorses():void
    {
        echo 'Today running : '.PHP_EOL;
        foreach ($this->simulator->getHorsesRaceInfo() as $horse)#
        {
            echo "Horse --> ".$horse->getRunner()." with bet coefficient : ".$horse->getBetKoef().PHP_EOL;
        }
    }
    public function PutBets():void
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









