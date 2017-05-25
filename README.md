# FormHandlerBundle

[![Build Status](https://travis-ci.org/SolidWorx/FormHandlerBundle.svg)](https://travis-ci.org/SolidWorx/FormHandlerBundle)

The FormHandler component attempts to make controllers with basic form handlers cleaner by off-loading form handling to separate classes.

# Table of Contents
- [Requirements](#requirements)
- [Installation](#installation)
    - [Composer](#composer)
- [Usage](#usage)
- [Testing](#testing)
- [Contributing](#contributing)
- [Licence](#licence)


## Requirements

FormHandler requires PHP 7.1+ and Symfony 3.0+

## Installation

### Composer

```bash
$ composer require solidworx/form-handler-bundle:^1.0
```

Then register the bundle in your Symfony application:

```php
<?php

// app/AppKernel.php

// ...
    public function registerBundles()
    {
        $bundles = [
            // ...
            new SolidWorx\FormHandler\FormHandlerBundle(),
        ];
        
        // ...
    )

```

## Usage

A form can have a class that implements the `FormHandlerInterface` interface. This interface exposes a single method in which the form can be retrieved:


```php
public function getForm(FormFactoryInterface $factory, Options $options);
```

This method can either return a standard form type, or use the factory to generate a form type.

```php
<?php

use Symfony\Component\Form\FormFactoryInterface;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\Options;

class MyFormHandler implements FormHandlerInterface
{
    public function getForm(FormFactoryInterface $factory, Options $options)
    {
        // either
        return MyFormType::class;

        // or
        return $factory->create(MyFormType::class);
    }
}
```

The benefit of using the factory, is when you need to pass additional information or options to the form, E.G

```php
return $factory->create(MyFormType::class, null, ['horizontal' => true]);
```

To register your form handler, register it as a service:

```yaml
services:
    my.form.handler:
        class: MyFormHandler
        tags:
            - { name: 'form.handler' }
```

Inside your controller, use the `form.handler` service to handle your form:

```php
<?php

class MyController extends Controller
{
    public function addAction()
    {
        return $this->get('solidworx.form_handler')->handle(MyFormHandler::class); // MyFormHandler will automatically be pulled from the container if it is tagges with `form.handler`
    }
}
```

This will process the necessary logic on the form (submit the form and handle the request etc).


If you need to handle a failed form, you need to implement the `FormHandlerFailInterface` interface:

```php
<?php

use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerFailInterface;
use Symfony\Component\Form\FormErrorIterator;

class MyFormHandler implements FormHandlerInterface, FormHandlerFailInterface
{
    // ...
    public function onFail(FormRequest $formRequest, FormErrorIterator $errors, $data = null)
    {
        // Form submission has failed, probably due to a validation error.
        // Handle it here if you need specific custom logic
    }
}
```

If you need to handle a successful form submission, implement the `FormHandlerSuccessInterface` interface:

```php
<?php

use SolidWorx\FormHandler\FormRequest;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerSuccessInterface;

class MyFormHandler implements FormHandlerInterface, FormHandlerSuccessInterface
{
    // ...
    public function onSuccess($data, FormRequest $form)
    {
        // $data is the submitted data from the form, do something with it here
        // This will probably save info to the DB
    }
}
```

## Adding options to a form

If you need to pass options to a form, you can add it as an array to the second argument of `FormHandler::handle`:

```php
<?php

class MyController extends Controller
{
    public function addAction()
    {
        return $this->get('solidworx.form_handler')->handle(MyFormHandler::class, ['entity' => new Blog]); // MyFormHandler will automatically be pulled from the container if it is tagges with `form.handler`
    }
}
```

The options will then be available in the `getForm` method as a `Options` object:

```php
<?php

use Symfony\Component\Form\FormFactoryInterface;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\Options;

class MyFormHandler implements FormHandlerInterface
{
    public function getForm(FormFactoryInterface $factory, Options $options)
    {
        return $factory->create(MyFormType::class, $options->get('entity'));
    }
}
```

You can also configure the options to set what options is allowed, set default values, define required options etc. by implementing the `FormHandlerOptionsResolver` interface:


```php
<?php

use Symfony\Component\OptionsResolver\OptionsResolver;
use SolidWorx\FormHandler\FormHandlerInterface;
use SolidWorx\FormHandler\FormHandlerOptionsResolver;

class MyFormHandler implements FormHandlerInterface, FormHandlerOptionsResolver
{
    // ...
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired('entity');
        $resolver->addAllowedTypes('entity', BlogEntity::class);
        $resolver->setDefault('another_options', 'myvalue');
    }
}
```

## Advanced Usage

That is the very basics of the component. There are more advanced usages where you can customize the handling of a form to your specific needs.

## Testing

To run the unit tests, execute the following command

```bash
$ vendor/bin/phpunit
```

## Contributing

See [CONTRIBUTING](https://github.com/SolidWorx/FormHandlerBundle/blob/master/CONTRIBUTING.md)

## License

FormHandler is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

Please see the [LICENSE](LICENSE) file for the full license.
