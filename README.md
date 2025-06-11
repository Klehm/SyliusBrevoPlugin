<p align="center">
    <img src="docs/banner.svg" alt="Banner showing Brevo and Sylius icons" />
</p>

<h1 align="center">Brevo Plugin</h1>
<p align="center">
    This plugin integrates your Sylius store with <a href=https://www.brevo.com/">Brevo</a>, an all-in-one AI-enabled platform to manage your customer relationships via Email, SMS, WhatsApp, Chat, and more..
</p>

## What does this plugin do?


The _SyliusBrevoPlugin_ allows you to send your transactional emails through Brevo.<br />
You can simply reuse the core HTML templates out-of-the-box or use Brevo transactional templates with the Drag & Drop editor.
<br />
<br />
Also, you can customize params with event listeners, check the templating [documentation](docs/templating.md).


## Where do I start?

This plugin is using the Brevo API. You must first : <br />
1. Create/have a Brevo account (free plan is supported)
2. Complete your profile, setup your sender email and verify your domain
3. Create an API key ([documentation](https://help.brevo.com/hc/en-us/articles/209467485-Create-and-manage-your-API-keys#h_01GW6ZQEKZ072SFGK03N9R6VE6))
4. Contact the support to be able to send emails through the API/SMTP (they ask for your store URL and check your identity)


## How can I install the plugin on my Sylius store?

Please, check the documentation at the [Installation](docs/installation.md) step.

## What's next?
Currently this plugin is "just" an email adapter. 
I plan to add full integration with Brevo services : 
- enrich data passed to templates for all Sylius core emails
- add commands to create core emails template in Brevo
- contacts synchronization with opt-in
- newsletter form subscription component

## License
This plugin is under the MIT license. See the complete license in the LICENSE file.<br/>
This is an unofficial plugin, I maintain this plugin free of charge and have no commercial ties to Brevo.

## Credits
Developed by [Klehm](https://clementmuller.fr/).
