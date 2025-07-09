<?php
declare(strict_types=1);

namespace AyhanKoyun\IupacNomenclature\Service;

/**
 * Summary of ChemicalStructureService
 *  @copyright (c) 2025 Ayhan Koyun
 */
class ChemicalStructureService
{
    /**
     * @param array $settings
     * @return array
     */
    public function getChemicalStructure(array $settings): array
    {
        return ['message' => 'it works'];
    }

    public function getChemicalStructureFromPostData(array $postData): array
    {
        $chainLength = (int) ($postData['chainLength'] ?? 0);
        $isCyclo = isset($postData['isCyclo']) && $postData['isCyclo'] === '1';
        $isAlcohol = isset($postData['isAlcohol']) && $postData['isAlcohol'] === '1';
        $substituents = [];
        $alcoholData = [];

        if (!empty($postData['substituents'])) {
            foreach ($postData['substituents'] as $s) {
                $substituents[] = [
                    'name' => $s['name'] ?? '',
                    'positions' => isset($s['positions']) ? explode(',', $s['positions']) : [],
                    'count' => (int) ($s['count'] ?? 0)
                ];
            }
        }

        if (!empty($postData['branchSubstituents'])) {
            foreach ($postData['branchSubstituents'] as $branch) {
                $substituents[] = [
                    'name' => $branch['branchName'] ?? '',
                    'positions' => isset($branch['branchPositions']) ? explode(',', $branch['branchPositions']) : [],
                    'count' => (int) ($branch['branchCount'] ?? 0),
                    'branchLength' => (int) ($branch['branchLength'] ?? 0),
                    'branchChainPosition' => (int) ($branch['branchChainPosition'] ?? 0),
                ];
            }
        }

        if (!empty($postData['halogens'])) {
            foreach ($postData['halogens'] as $halogen) {
                $substituents[] = [
                    'name' => $halogen['halogen'] ?? '',
                    'positions' => isset($halogen['halogenPositions']) ? explode(',', $halogen['halogenPositions']) : [],
                    'count' => (int) ($halogen['halogenCount'] ?? 0)
                ];
            }
        }

        if (!empty($postData['alcohols'])) {
            foreach ($postData['alcohols'] as $val) {
                $positions = array_map('trim', explode(',', $val['alcoholPositions'] ?? ''));
                $positions = array_map('intval', $positions);

                $alcoholData[] = [
                    'positions' => $positions,
                    'count' => (int) ($val['alcoholCount'] ?? 1),
                ];
            }
        }

        // Jetzt binden wir deine Klassen ein:
        $alkane = new \AyhanKoyun\IupacNomenclature\Service\Alkane(
            $chainLength,
            $substituents,
            $isCyclo,
            $isAlcohol,
            $alcoholData
        );

        $iupacName = $alkane->getName();

        return [
            'iupac' => $iupacName,
            'chainLength' => $chainLength,
            'substituents' => $substituents,
            'isCyclo' => $isCyclo,
            'isAlcohol' => $isAlcohol
        ];
    }
}
