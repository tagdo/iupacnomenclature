# Contributing to IUPAC Nomenclature Extension

Thank you for your interest in the IUPAC Nomenclature Extension! We welcome all contributions.

## How You Can Contribute

### Reporting Bugs

1. Check if the bug has already been reported
2. Create an issue with a clear description
3. Add steps to reproduce
4. Describe expected and actual behavior

### Feature Requests

1. Create an issue with the "enhancement" label
2. Describe the desired feature in detail
3. Explain the benefit to the community

### Contributing Code

#### Prerequisites

- PHP 8.0 or higher
- Composer
- TYPO3 CMS 12.x
- Git

#### Setting up Development Environment

1. Fork the repository
2. Clone your fork locally
3. Install dependencies:
   ```bash
   composer install
   ```
4. Run tests:
   ```bash
   composer test
   ```

#### Coding Standards

- Follow PSR-12 coding standards
- Use PHP 8.0+ features
- Write unit tests for new functionality
- Document all public methods
- Use English comments and documentation

#### Creating Pull Requests

1. Create a feature branch
2. Implement your changes
3. Write tests for new functionality
4. Run all tests
5. Update documentation
6. Create a pull request

### Pull Request Checklist

- [ ] Code follows PSR-12 standards
- [ ] Unit tests are added/updated
- [ ] Documentation is updated
- [ ] CHANGELOG.md is updated
- [ ] All tests pass
- [ ] Code review is completed

## Development

### Project Structure

```
Classes/
├── Controller/          # TYPO3 Controllers
├── Service/            # Business Logic
└── Domain/             # Domain Models

Tests/
├── Unit/               # Unit Tests
└── Functional/         # Functional Tests

Configuration/          # TYPO3 Configuration
Resources/              # Templates, CSS, JS
```

### Tests

- Unit tests: `composer test`
- Coverage: `composer test-coverage`
- PHPStan: `composer analyse`

### Release Process

1. Update version in `composer.json`
2. Update CHANGELOG.md
3. Create tag: `git tag v1.0.0`
4. Push: `git push origin v1.0.0`

## Communication

- Issues: GitHub Issues
- Discussions: GitHub Discussions
- Email: ayhankoyun@hotmail.de

## License

By contributing to this project, you agree that your contributions will be licensed under the MIT License. 