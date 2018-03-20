# Drupal 8 Production Checklist

A checklist based on the active configuration, focused on Drupal 8, for site launch and maintenance.

## Scope

Sections covered by this checklist:

- System wide status and reports
- Contributed projects review
- Vendors, custom code and libraries
- Spam related configuration and modules
- Security and access control
- Content model review and proofreading
- Frontend basic checks
- Database and configuration
- Performance and caching configuration
- Various test coverage
- Analytics
- Server configuration and backups
- Basic SEO
- Legal aspects
- Project documentation

## Installation

`composer require drupal/production_checklist`

Start ticking via _Configuration > Development > Production Checklist_

Optionally add or remove sections.

## Roadmap

Features to be implemented in forthcoming releases.

### Filters

Due to the amount of items to check, filters can provide more context across sections.

- Musth have / nice to have
- Launch / release / maintenance

### Notification

Be notified once a checked item has been invalidated by configuration (e.g. 'Email obfuscation' has been marked has checked but a new email field has been created and has not been protected against email address harvesting).
