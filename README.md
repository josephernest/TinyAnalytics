# TinyAnalytics

<img src="http://gget.it/27cgzhtl/TinyAnalytics.png" width="900"/>

**TinyAnalytics** is a lightweight web analytics tool based on the idea that:

* The two most useful things are: **number of unique visitors per day** (with a nice chart) and **list of referers** who send some traffic to your websites,

* It should give the idea of the traffic, even for multiple websites, **on a single dashboard** (without having to click in lots of menu items to change the currently displayed website, etc.),

* It should be fast and lightweight.

If you're looking for more informations than those (such as country, browser, screen resolution, time spent on a page, etc.), then **TinyAnalytics** is not the right tool for you. Please try [Google Analytics](https://analytics.google.com), [Open Web Analytics](https://www.openwebanalytics.com/) or [Piwik](https://www.piwik.org/) instead. I personally found the two last ones [not very handy for me](http://josephbasquin.fr/aboutanalytics).

> After years, I've noticed that **I prefer to have few (important) informations that I can consult each day in 30 seconds**, rather than lots of informations for which I would need 15 or 30 minutes per day for an in-depth analysis.

## Install

There are three easy steps:

1) Unzip this package in a directory, e.g. `/var/www/TinyAnalytics/`.

2) Add the following tracking code to your websites at then end of `.php` files, e.g. `/var/www/mywebsite/index.php`:

    ~~~
    <?php 
    include '/var/www/TinyAnalytics/tracker.php';
    record_visit('mywebsite');
    ?>
    ~~~~

3) Modify your password in the first lines of `index.php`. Default password is `abcdef`.    

It's done! Visit at least one of your tracked websites, and open `TinyAnalytics/index.php` in your browser!

## About

Author: Joseph Ernest ([@JosephErnest](https://twitter.com/JosephErnest))

Other projects: [BigPicture](https://bigpictu.re), [bigpicture.js](https://github.com/josephernest/bigpicture.js), [AReallyBigPage](https://github.com/josephernest/AReallyBigPage), [SamplerBox](https://www.samplerbox.org), [Void](http://github.com/josephernest/void), [TalkTalkTalk](https://github.com/josephernest/TalkTalkTalk), [YellowNoiseAudio](https://www.yellownoiseaudio.com), [bloggggg](https://github.com/josephernest/bloggggg), etc.

Thanks to [WhiteHat](http://stackoverflow.com/users/5090771/whitehat) for his help on the chart visualization design.

## Sponsors and consulting

I am available for Python, Data science, ML, Automation consulting. Please contact me on https://afewthingz.com for freelancing requests.

Do you want to support the development of my open-source projects? Please contact me!

I am currently sponsored by [CodeSigningStore.com](https://codesigningstore.com/). Thank you to them for providing a DigiCert Code Signing Certificate and supporting open source software.

## Other versions

Here is [PHP-only version](https://github.com/benyafai/TinyAnalytics) contributed by @benyafai.

## License

MIT license
