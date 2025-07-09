<?php

namespace AyhanKoyun\IupacNomenclature\Service;

use AyhanKoyun\IupacNomenclature\Service\Compound;

/**
 * Summary of Alkane
 *  @copyright (c) 2025 Ayhan Koyun
 */
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

        /**
         * Hundreds
         * If the hundreds digit is 0, we skip it.
         * Otherwise, we append the corresponding hundreds prefix.
         * For example, 100 → 'hect', 200 → 'dict', etc
         */
        if ($hundredsDigit > 0) {
            $name .= $hundreds[$hundredsDigit];
        }

        /**
         * Tens
         * If the tens digit is 0, we skip it.
         * Otherwise, we append the corresponding tens prefix.
         * For example, 10 → 'dec', 20 → 'icos', etc.
         */
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
            /**
             * 1) Alphabetical comparison
             * If substituent names are identical, sort by position.
             * This is important for the IUPAC rules.
             */
            $nameComparison = strtolower($a['name']) <=> strtolower($b['name']);
            if ($nameComparison === 0) {
                /**
                 * 2) If substituent names are identical, sort by position
                 * This ensures that substituents with the same name are sorted by their first position.
                 * For example, if both substituents are "methyl" at positions 2 and 3,
                 * they will be sorted by their first position (2 <=> 3
                 */
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

        /**
         * Branch length
         * If the substituent has a branch length, we create a special name.
         * For example, if the substituent is "methyl" at position 2 with a branch length of 3,
         * the name would be "2-(1-methylpropyl)".
         */
        if (isset($substituent['branchLength'])) {
            $branchName = strtolower($this->getAlkaneBase($substituent['branchLength'])) . 'yl';
            return $substituent['branchChainPosition']
                . "-(1-" . $name . $branchName . ")";
        }

        /**
         * Halogenes (F, Cl, Br, I) German use case, in english fluoro, chloro, bromo, iodo
         * If the substituent is a halogen (F, Cl, Br, I),
         * we use uppercase letters for the halogen names.
         * For example, "f" becomes "F", "cl" becomes "Cl",
         * "br" becomes "Br", and "i" becomes "I".
         * This is important for the IUPAC rules.
         */
        if (in_array($name, ['f', 'cl', 'br', 'i'])) {
            return $positions . '-' . ucfirst($name);
        }

        /**
         * Standard: e.g. 2,3,5-trimethyl
         * If the substituent is not a halogen, we create the standard name.
         * For example, if the substituent is "methyl" at positions 2, 3, and 5,
         * the name would be "2,3,5-trimethyl".
         * This is important for the IUPAC rules.
         */
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
        /**
         * All letters in lowercase
         */
        $iupacName = strtolower($iupacName);

        /**
         * First letter A-Z uppercase
         * This is a special case for the first letter of the IUPAC name.
         * We use a callback to replace the first lowercase letter with its uppercase equivalent.
         * For example, "methane" becomes "Methane".
         */
        $iupacName = preg_replace_callback(
            '/[a-z]/',
            fn($m) => strtoupper($m[0]),
            $iupacName,
            1 // nur erstes Vorkommen ersetzen
        );

        /**
         * Halogenes (F, Cl, Br, I) → are upper case (German use case)
         */
        $iupacName = preg_replace_callback(
            '/(?<=^|[\d,-])(f|cl|br|i)/',
            fn($m) => ucfirst($m[0]),
            $iupacName
        );

        return $iupacName;
    }

    /**
     * Alkenes and alkines are not supported in this class.
     * This method is here for compatibility with the parent class.
     * It does not create an IUPAC name for alkenes and alkines.
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

        /**
         * 1) Alphabetically sort substituents
         */ 
        /**
         * (This is important for the ring logic)
         */ 
        $substituents = $this->sortSubstituentsAlphabetically($substituents);

        /**
         * (2) Find the smallest position of the first substituent
         * (This is important for the ring logic)
         */

        $minPos = min($substituents[0]['positions']);

        /**
         * (3) Move ALL positions for the first substituent
         * (This is important for the ring logic)
         */    

        $newPositionsForFirst = [];
        foreach ($substituents[0]['positions'] as $pos) {
        /**
         * * first position is 1-based
         * Shift positions to ensure they are 1-based and cyclic
         */

            $shifted = (($pos - $minPos + $chainLength) % $chainLength) + 1;
            $newPositionsForFirst[] = $shifted;
        }
        sort($newPositionsForFirst);
        $substituents[0]['positions'] = $newPositionsForFirst;

        /**
         * (4) Move ALL positions for the other substituents
         * This is important for the ring logic.
         */ 

        for ($i = 1; $i < count($substituents); $i++) {
            $updatedPositions = [];
            foreach ($substituents[$i]['positions'] as $pos) {
                $shifted = (($pos - $minPos + $chainLength) % $chainLength) + 1;
                $updatedPositions[] = $shifted;
            }
            sort($updatedPositions);
            $substituents[$i]['positions'] = $updatedPositions;
        }

        /**
         * (5) Resort substituents, build names etc.
         * This is important for the ring logic.
         */
        usort($substituents, function ($a, $b) {
            $posA = min($a['positions']);
            $posB = min($b['positions']);
            if ($posA === $posB) {
                return strtolower($a['name']) <=> strtolower($b['name']);
            }
            return $posA <=> $posB;
        });

        /**
         * 6) Substituentenname creation
         * Build the names of the substituents based on their positions and names.
         */
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
        /**
         * 1) Sorts substituents alphabetically
         *    (This is important for the IUPAC rules)
         */
        $sortedSubstituents = $this->sortSubstituentsAlphabetically($this->substituents);

        /**
         * 2) Create substituent names e.g. "2-methyl", "3-Cl", ...
         *    - This is important for the IUPAC rules.
         *    - The names are created based on the substituent positions and names.
         *    - For example, if the substituent is "methyl" at position 2,
         *      the name would be "2-methyl".
         */
        $substituentNames = [];
        foreach ($sortedSubstituents as $sub) {
            $substituentNames[] = $this->createSubstituentName($sub);
        }

        /**
         * 3) How many OH groups are there in total?
         *    - e.g. sum $this->alcoholData[$i]['count']
         */
        $totalOH = 0;
        $ohPositionsAll = []; // Count all positions
        foreach ($this->alcoholData as $oh) {
            $totalOH += $oh['count'];
            /**
             * * e.g. positions = [2,3], count=2 -> Then there are 2 OH groups at 2 and 3,
             * or 1 at 2 and 1 at 3. Depending on the logic
             */
            foreach ($oh['positions'] as $pos) {
                $ohPositionsAll[] = $pos;
            }
        }
        sort($ohPositionsAll);

        /**
         * 4) Suffix for the OH group(s)
         *    e.g. 1 => "ol", 2 => "diol",
         *    3 => "triol" etc.
         *    - This is important for the IUPAC rules.
         *    - The suffix is determined based on the total number of OH groups.
         *    - For example, if there is 1 OH group, the suffix is "ol",
         *      if there are 2 OH groups, the suffix is "diol",
         *      if there are 3 OH groups, the suffix is "triol", etc
         *    - This is important for the IUPAC rules.
         *    - The suffix is determined based on the total number of OH groups.
         */
        $suffix = $this->getOhSuffix($totalOH);

        /**
         * 5) Basename without "an" at the end
         * E.g. for chain length = 4 => "but"
         * Then "but" + "an" => "butan" we want "butan-2-ol"
         * or directly: "but" + "an" + "ol" => "butan-2-ol"
         */
        $base = $this->getAlkaneBase($this->chainLength);

        /**
         * 6) OH-Positions
         *    - If there are no OH groups, this will be an empty string.
         *    - If there are OH groups, we create a string with the positions.
         *      For example, if there are OH groups at positions 2 and 3,
         *      the string would be "2,3-". 
         *      - The "-" at the end is important for the IUPAC rules.
         *      - It indicates that the positions are followed by the suffix.
         *      - If there are no OH groups, this will be an empty string.
         *      - If there are OH groups, we create a string with the positions.
         *        For example, if there are OH groups at positions 2 and 3,
         *        the string would be "2,3-".
         *        - The "-" at the end is important for the IUPAC rules.
         */ 
        $ohPositionString = '';
        if (!empty($ohPositionsAll)) {
            $ohPositionString = implode(',', $ohPositionsAll) . '-';
        }

        /**
         * 7) Create Alcohol name
         *    E.g. "butan" => "butan-2,3-diol" (simplified)
         *    =? "butan" + "-" + "2,3-" + " 
         *    - This is a minimal solution:
         *    - The alkanol name is created by combining the base name,
         *      the OH position string, and the suffix.
         */

        $rawAlkanName = $base . 'an';      // "butan"
        $alkanolName = $rawAlkanName
            . '-'
            . $ohPositionString
            . $suffix;
        $full = '';
        if (!empty($substituentNames)) {
            $full = implode('-', $substituentNames) . $alkanolName;
        } else {
            $full = $alkanolName;
        }

        /**
         * 8) Substituent names + Alcohol name build
         * Return the formatted IUPAC name.
         */
 
        return $this->formatIupacName($full);
    }

    public function createCycloAlkanolName()
    {
        /**
         * 1) Sorts substituents alphabetically
         *    (This is important for the IUPAC rules)
         *    - This ensures that substituents are sorted by their names.
         */

        $subs = $this->sortSubstituentsAlphabetically($this->substituents);

        /**
         * 2) How many OH groups are there in total?
         *    - e.g. sum $this->alcoholData[$i]['count']
         *    - This is important for the IUPAC rules.
         *    - The total number of OH groups is determined by summing the counts of all
         *      alcohol data entries.
         */

        $totalOH = 0;
        $ohPositionsAll = [];
        foreach ($this->alcoholData as $oh) {
            $totalOH += $oh['count'];
            foreach ($oh['positions'] as $pos) {
                $ohPositionsAll[] = $pos;
            }
        }
        sort($ohPositionsAll);
        $suffix = $this->getOhSuffix($totalOH); 

        /**
         * 3) Create the base name
         *    - E.g. "cyclo" + base + "an" = "cyclohexan"
         *    - Then replace "an" with "ol" (or "diol")
         *    - This is a minimal solution:
         *    - The base name is created by combining "cyclo" with the alkane base name
         *      and "an". For example, if the chain length is 6,
         *      the base name would be "cyclohexan".
         */
        $base = 'cyclo' . $this->getAlkaneBase($this->chainLength) . 'an';

        /**
         * 4) Create the OH positions, e.g. "1,2-"
         * - If there are no OH groups, this will be an empty string.
         */   
        $ohPosStr = '';
        if (!empty($ohPositionsAll)) {
            $ohPosStr = implode(',', $ohPositionsAll) . '-';
        }

        /**
         * 5) Create the alkanol name
         *    - E.g. "cyclohexan" =? "cyclohexan-1,2-diol"
         *    - =? "cyclohex" + "an" + "1,2-" + "diol"
         *    - This is a minimal solution:
         *    - The alkanol name is created by combining the base name
         *      without "an" at the end, the OH position string, and the suffix
         */
        
        $baseWithoutAn = substr($base, 0, -2); // "cyclohex"

        $alkanolName = $baseWithoutAn
            . 'an-'
            . $ohPosStr
            . $suffix;
        /**
         * 6) Substituent names + Alcohol name build
         *    - E.g. "cyclohexan-1,2-diol"
         *    - The final IUPAC name is created by combining the substituent names
         *      and the alkanol name.
         */ 
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
