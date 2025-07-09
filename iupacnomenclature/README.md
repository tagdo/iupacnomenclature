# IUPAC Nomenclature Extension

A TYPO3 CMS extension for automatic generation of IUPAC nomenclature for chemical structures (only (cyclo)-alkanes).

## Description

This extension provides a comprehensive solution for automatically naming chemical compounds according to IUPAC rules. It supports various classes of organic compounds and can be used in TYPO3 CMS 12.x.

## Features

- **Automatic IUPAC Nomenclature**: Generates correct IUPAC names for chemical structures
- **Alkane Support**: Complete naming of alkanes (methane, ethane, propane, etc.)
- **Extensible Architecture**: Modular structure for easy extensions
- **TYPO3 CMS Integration**: Seamless integration with TYPO3 CMS 12.x
- **Frontend Controllers**: Provides frontend functionality

## Installation

### Via Composer (recommended)

```bash
composer require ayhan-koyun/iupacnomenclature
```

### Manual Installation

1. Download the extension
2. Extract it to the `packages/` folder of your TYPO3 project
3. Install the extension via TYPO3 Extension Manager

## Usage

### Backend

After installation, a new backend module will be available:

1. Go to TYPO3 Backend
2. Navigate to the "IUPAC Nomenclature" module
3. Enter a chemical structure
4. Get the correct IUPAC name generated

### Frontend

The extension provides frontend controllers that you can use in your templates:

```html
<f:render section="chemicalStructure" arguments="{structure: 'CH4'}" />
```

### Programmatic Usage

```php
use AyhanKoyun\IupacNomenclature\Service\ChemicalStructureService;

$service = GeneralUtility::makeInstance(ChemicalStructureService::class);
$iupacName = $service->generateIUPACName('CH4'); // Returns "methane"
```

## System Requirements

- TYPO3 CMS 12.0 or higher
- PHP 8.0 or higher
- Composer

## Configuration

The extension can be configured via TYPO3 configuration. See documentation for more details.

## Development

### Project Structure

```
Classes/
├── Controller/
│   └── ReviewController.php
└── Service/
    ├── Alkane.php
    ├── ChemicalStructureService.php
    ├── Compound.php
```

### Running Tests

```bash
composer test
```

## Contributing

Contributions are welcome! Please note:

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Create a pull request

## License

This extension is licensed under the MIT License. See [LICENSE](LICENSE) for details.

## Support

For questions or issues:

- Create an issue on GitHub
- Contact the author: ayhankoyun@hotmail.de

## Changelog

### Version 1.0.0
- Initial release
- Basic IUPAC nomenclature functionality
- Alkane support
- TYPO3 CMS 12.x integration

## Roadmap

- [ ] Support for alkenes and alkynes
- [ ] Aromatic compounds
- [ ] Functional group detection
- [ ] 3D structure visualization
- [ ] API endpoints for external integrations 