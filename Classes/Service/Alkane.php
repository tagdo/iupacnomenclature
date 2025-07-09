<?php

namespace AyhanKoyun\IupacNomenclature\Service;

use AyhanKoyun\IupacNomenclature\Service\Compound;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class Alkane extends Compound
{
    /**
     * @var bool $isCyclo
     */
    protected $isCyclo = false;

    /**
     * @var bool $isAlcohol
     */
    protected $isAlcohol = false;

    /**
     * @var array $alcoholData
     */
    protected $alcoholData = [];


    public function __construct(
        $chainLength,
        $substituents = [],
        $isCyclo = false,
        $isAlcohol = false,
        $alcoholData = []
    ) {
        $this->chainLength = $chainLength;
        $this->substituents = $substituents;
        $this->isCyclo = $isCyclo;
        $this->isAlcohol = $isAlcohol;
        $this->alcoholData = $alcoholData;
    }

    /**
     * Returns the (simple) IUPAC name of a (non-cyclic) alkane.
     */
    public function getName()
    {
        if ($this->isAlcohol) {
            if ($this->isCyclo) {
                return $this->createCycloAlkanolName();
            } else {
                return $this->createAlkanolName();
            }
        } else {
            if ($this->isCyclo) {
                return $this->createCycloIupacName($this->chainLength, $this->substituents);
            } else {
                $sortedSubstituents = $this->sortSubstituentsAlphabetically($this->substituents);

                $substituentNames = [];
                foreach ($sortedSubstituents as $substituent) {
                    $substituentNames[] = $this->createSubstituentName($substituent);
                }

                $iupacName = implode('-', $substituentNames)
                    . $this->getAlkaneBase($this->chainLength)
                    . 'an';

                return $this->formatIupacName($iupacName);
            }
        }
    }

    /**
     * Determines the base chain name (meth, eth, prop, ...) depending on the chain length.
     */
    public function getAlkaneBase(int $length): string
    {
        if ($length < 1 || $length > 1000) {
            return 'Unknown';
        }

        $units = [
            1 => 'meth',
            2 => 'eth',
            3 => 'prop',
            4 => 'but',
            5 => 'pent',
            6 => 'hex',
            7 => 'hept',
            8 => 'oct',
            9 => 'non'
        ];

        $prefixes = [
            1 => 'hen',
            2 => 'do',
            3 => 'tri',
            4 => 'tetra',
            5 => 'penta',
            6 => 'hexa',
            7 => 'hepta',
            8 => 'octa',
            9 => 'nona'
        ];

        $tens = [
            1 => 'dec',
            2 => 'icos',
            3 => 'triacont',
            4 => 'tetracont',
            5 => 'pentacont',
            6 => 'hexacont',
            7 => 'heptacont',
            8 => 'octacont',
            9 => 'nonacont'
        ];

        $hundreds = [
            1 => 'hect',
            2 => 'dict',
            3 => 'trict',
            4 => 'tetract',
            5 => 'pentact',
            6 => 'hexact',
            7 => 'heptact',
            8 => 'octact',
            9 => 'nonact',
            10 => 'kili'
        ];

        if ($length <= 12) {
            return [
                1 => 'meth',
                2 => 'eth',
                3 => 'prop',
                4 => 'but',
                5 => 'pent',
                6 => 'hex',
                7 => 'hept',
                8 => 'oct',
                9 => 'non',
                10 => 'dec',
                11 => 'undec',
                12 => 'dodec'
            ][$length];
        }

        $hundredsDigit = intdiv($length, 100);
        $remainder = $length % 100;
        $tensDigit = intdiv($remainder, 10);
        $unitsDigit = $remainder % 10;

        $name = '';

        // Hundrets
        if ($hundredsDigit > 0) {
            $name .= $hundreds[$hundredsDigit];
        }

        // Tens
        if ($tensDigit > 0) {
            $name .= $tens[$tensDigit];
        }

        // One's
        if ($unitsDigit > 0) {
            $prefix = $prefixes[$unitsDigit] ?? '';
            if ($tensDigit > 0) {
                $name = $prefix . $name;
            } else {
                $name .= $prefix;
            }
        }

        return $name;
    }



    /**
     * Sorts substituents alphabetically (and by first position in case of equality).
     */
    public function sortSubstituentsAlphabetically($substituents)
    {
        usort($substituents, function ($a, $b) {
            // 1) Alphabetical comparison
            $nameComparison = strtolower($a['name']) <=> strtolower($b['name']);
            if ($nameComparison === 0) {
                // 2) If substituent names are identical, sort by position
                return $a['positions'][0] <=> $b['positions'][0];
            }
            return $nameComparison;
        });
        return $substituents;
    }

    /**
     * Create the (partial) name of a substituent based on
     * position information, prefix (di-, tri- etc.) and possibly branch length.
     */
    public function createSubstituentName($substituent)
    {
        $prefix = $this->getPrefix($substituent['count']);
        $positions = implode(',', $substituent['positions']);
        $name = strtolower($substituent['name']);

        // Branch length 
        if (isset($substituent['branchLength'])) {
            $branchName = strtolower($this->getAlkaneBase($substituent['branchLength'])) . 'yl';
            return $substituent['branchChainPosition']
                . "-(1-" . $name . $branchName . ")";
        }

        // Halogenes (F, Cl, Br, I) → are upper case
        if (in_array($name, ['f', 'cl', 'br', 'i'])) {
            return $positions . '-' . ucfirst($name);
        }

        // Standard: e.g. 2,3,5-trimethyl
        return $positions . '-' . $prefix . strtolower($name);
    }


    /**
     * Get the prefix for a substituent based on its count.
     * For example, 1 → '', 2 → 'di', 3 → '
     */
    public function getPrefix($count)
    {
        $prefixes = [
            1 => '',
            2 => 'di',
            3 => 'tri',
            4 => 'tetra',
            5 => 'penta',
            6 => 'hexa',
            7 => 'hepta',
            8 => 'octa',
            9 => 'nona'
        ];
        return $prefixes[$count] ?? '';
    }

    /**
     * Formats the IUPAC name according to the rules:
     * - All letters are lowercase.
     */
    public function formatIupacName(string $iupacName): string
    {
        // All letters in lowercase
        $iupacName = strtolower($iupacName);

        // First letter A-Z uppercase
        // This is a special case for the first letter of the IUPAC name.
        $iupacName = preg_replace_callback(
            '/[a-z]/',
            fn($m) => strtoupper($m[0]),
            $iupacName,
            1 // nur erstes Vorkommen ersetzen
        );

        // Halogenes (F, Cl, Br, I) → are upper case
        $iupacName = preg_replace_callback(
            '/(?<=^|[\d,-])(f|cl|br|i)/',
            fn($m) => ucfirst($m[0]),
            $iupacName
        );

        return $iupacName;
    }

    /**
     * Creates the IUPAC name for a linear alkane,
     * considering the IUPAC rules:
     */
    public function createIupacName($chainLength, $substituents)
    {
        $sortedSubstituents = $this->sortSubstituentsAlphabetically($substituents);
        $parts = [];
        foreach ($sortedSubstituents as $substituent) {
            $parts[] = $this->createSubstituentName($substituent);
        }
        $name = implode('-', $parts) . $this->getAlkaneBase($chainLength) . "an";
        return $this->formatIupacName($name);
    }

    /**
     * NEW - Creates the IUPAC name for a cyclic alkane,
     * considering the IUPAC rules:
     */
    public function createCycloIupacName($chainLength, $substituents)
    {

        // (1) Alphabetically sort substituents
        //    (This is important for the ring logic)
        $substituents = $this->sortSubstituentsAlphabetically($substituents);

        // (2) Find the smallest position of the first substituent
        //    (This is important for the ring logic)
        $minPos = min($substituents[0]['positions']);

        // (3) Move ALL positions for the first substituent
        //    (This is important for the ring logic)
        $newPositionsForFirst = [];
        foreach ($substituents[0]['positions'] as $pos) {
            // first position is 1-based
            // Shift positions to ensure they are 1-based and cyclic
            $shifted = (($pos - $minPos + $chainLength) % $chainLength) + 1;
            $newPositionsForFirst[] = $shifted;
        }
        sort($newPositionsForFirst);
        $substituents[0]['positions'] = $newPositionsForFirst;

        // (4) Move ALL positions for the other substituents
        //    (This is important for the ring logic)
        for ($i = 1; $i < count($substituents); $i++) {
            $updatedPositions = [];
            foreach ($substituents[$i]['positions'] as $pos) {
                $shifted = (($pos - $minPos + $chainLength) % $chainLength) + 1;
                $updatedPositions[] = $shifted;
            }
            sort($updatedPositions);
            $substituents[$i]['positions'] = $updatedPositions;
        }

        // (5) Resort substituents, build names etc. (as usual)
        //    This is important for the ring logic.
        usort($substituents, function ($a, $b) {
            $posA = min($a['positions']);
            $posB = min($b['positions']);
            if ($posA === $posB) {
                return strtolower($a['name']) <=> strtolower($b['name']);
            }
            return $posA <=> $posB;
        });

        // (6) Substituentenname creation
        //    Build the names of the substituents based on their positions and names.
        $parts = [];
        foreach ($substituents as $sub) {
            $parts[] = $this->createSubstituentName($sub);
        }

        $base = 'cyclo' . $this->getAlkaneBase($chainLength) . 'an';
        $rawName = implode('-', $parts) . $base;

        return $this->formatIupacName($rawName);
    }

    public function createAlkanolName()
    {
        // 1) Sorts substituents alphabetically
        //    (This is important for the IUPAC rules)
        $sortedSubstituents = $this->sortSubstituentsAlphabetically($this->substituents);

        // 2) Create substituent names e.g. "2-methyl", "3-chloro", ...
        $substituentNames = [];
        foreach ($sortedSubstituents as $sub) {
            $substituentNames[] = $this->createSubstituentName($sub);
        }

        // 3) How many OH groups are there in total?
        //    (e.g. sum $this->alcoholData[$i]['count'])
        $totalOH = 0;
        $ohPositionsAll = []; // Count all positions
        foreach ($this->alcoholData as $oh) {
            $totalOH += $oh['count'];
            // e.g. positions = [2,3], count=2 -> Then there are 2 OH groups at 2 and 3,
            // or 1 at 2 and 1 at 3. Depending on the logic
            foreach ($oh['positions'] as $pos) {
                $ohPositionsAll[] = $pos;
            }
        }
        sort($ohPositionsAll);

        // 4) Suffix for the OH group(s)
        //    e.g. 1 => "ol", 2 => "diol", 3 => "triol" etc.
        $suffix = $this->getOhSuffix($totalOH);

        // 5) Basename without "an" at the end
        //    E.g. for chain length = 4 => "but"
        //    Then "but" + "an" => "butan" => we want "butan-2-ol"
        //    or directly: "but" + "an" + "ol" => "
        $base = $this->getAlkaneBase($this->chainLength);

        // 6) OH-Positions 
        $ohPositionString = '';
        if (!empty($ohPositionsAll)) {
            $ohPositionString = implode(',', $ohPositionsAll) . '-';
            // e.g. "2,3-"
        }

        // 7) Create Alcohol name
        //    E.g. "butan" =? "butan-2,3-diol" (simplified)
        //    =? "butan" + "-" + "2,3-" + " 
        $rawAlkanName = $base . 'an';      // "butan"
        // z. B. "butan-2-ol" =>    "butan" + "-" + "2-" + "ol"
        //       "butan-2,3-diol" => "butan" + "-" + "2,3-" + "diol"

        $alkanolName = $rawAlkanName
            . '-'
            . $ohPositionString
            . $suffix;
        // z. B. "butan-2,3-diol"

        // 8) Substituentnamen + Alcoholname build 
        //    Build the full name by combining substituent names and the alkanol name.
        //    If there are no substituents, just return the alkanol name.
        $full = '';
        if (!empty($substituentNames)) {
            $full = implode('-', $substituentNames) . $alkanolName;
        } else {
            $full = $alkanolName;
        }

        // 9) Format & return
        //    Return the formatted IUPAC name.
        return $this->formatIupacName($full);
    }

    public function createCycloAlkanolName()
    {
        // 1) Sorts substituents alphabetically
        //    (This is important for the IUPAC rules)
        $subs = $this->sortSubstituentsAlphabetically($this->substituents);

        // 2) OH how many, which positions?
        $totalOH = 0;
        $ohPositionsAll = [];
        foreach ($this->alcoholData as $oh) {
            $totalOH += $oh['count'];
            foreach ($oh['positions'] as $pos) {
                $ohPositionsAll[] = $pos;
            }
        }
        sort($ohPositionsAll);
        $suffix = $this->getOhSuffix($totalOH); // z. B. "ol" oder "diol" ...

        // 3) Simple way: "cyclo" + base + "an" = "cyclohexan"
        //    Then replace "an" =? "ol" (or "diol")
        $base = 'cyclo' . $this->getAlkaneBase($this->chainLength) . 'an';

        // 4) Create the OH positions, e.g. "1,2-"
        //    - If there are no OH groups, this will be an empty string.
        $ohPosStr = '';
        if (!empty($ohPositionsAll)) {
            $ohPosStr = implode(',', $ohPositionsAll) . '-';
        }

        // 5) Substitute an at the end
        //    E.g. "cyclohexan" =? "cyclohexan-1,2-diol"
        //    =? "cyclohex" + "an" + "1,2-" + "diol"
        //    - This is a minimal solution:
        
        $baseWithoutAn = substr($base, 0, -2); // "cyclohex"

        $alkanolName = $baseWithoutAn
            . 'an-'
            . $ohPosStr
            . $suffix;
        // e. g. "cyclohexan-1,2-diol"

        // 6) Substituenten (Methyl, Ethyl, Halogen) etc.
        //    You could move/rename them analogously to createCycloIupacName
        $substituentNames = [];
        foreach ($subs as $sub) {
            $substituentNames[] = $this->createSubstituentName($sub);
        }

        $full = implode('-', $substituentNames) . $alkanolName;

        return $this->formatIupacName($full);
    }

    private function getOhSuffix(int $count): string
    {
        $map = [
            1 => 'ol',
            2 => 'diol',
            3 => 'triol',
            4 => 'tetraol',
            5 => 'pentaol',
            6 => 'hexaol',
            7 => 'heptaol',
            8 => 'octaol',
            9 => 'nonaol',
            10 => 'decaol',
            11 => 'hendecaol',
            12 => 'dodecaol',
            13 => 'tridecaol',
            14 => 'tetradecaol',
            15 => 'pentadecaol',
            16 => 'hexadecaol',
            17 => 'heptadecaol',
            18 => 'octadecaol',
            19 => 'nonadecaol',
            20 => 'eicosaol',
            21 => 'henicosaol',
            22 => 'docosaol',
            23 => 'tricosaol',
            24 => 'tetracosaol',
            25 => 'pentacosaol',
            26 => 'hexacosaol',
            27 => 'heptacosaol',
            28 => 'octacosaol',
            29 => 'nonacosaol',
            30 => 'triacontaol',
        ];

        return $map[$count] ?? 'undefined';
    }

}
