# Forminator Extension 1.0.0v

## Author

- [@theridozu](https://github.com/TheRidozu)


## Features

- Double check e-mail
- Limit submissions



## Documentation

### Shortcode: [forminator_form_ext]

#### Description:

The shortcode [forminator_form_ext] is used to display forms created with the Forminator plugin.

#### Attributes: 

1. id ( optional )
 - Forminator form id
 - Default value: null
 - Example: [forminator_form_ext id=10]
2. limit ( optional )
 - submissions limit
 - Default value: 30
 - Example: [forminator_form_ext limit=40]

#### Notes
1. Ensure that Forminator plugin is installed.
2. Ensure that the form with the provided ID exists in the system.
3. Ensure that forminator form has fields email-1 & email-2.