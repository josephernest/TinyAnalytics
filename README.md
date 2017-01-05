# TinyAnalytics

<img src="http://gget.it/27cgzhtl/TinyAnalytics.png" width="900"/>

**TinyAnalytics** is a lightweight web analytics tool based on the idea that:

* The two most useful things are: **number of unique visitors per day** (with a nice chart) and **list of referers** who send some traffic to your websites,

* It should give the idea of the traffic **in one single page**, even for multiple websites (without having to click in lots of menu items to change the currently displayed website, etc.),

* It should be fast and lightweight.

If you're looking for more informations than those (such as country, browser, screen resolution, time spent on a page, etc.), then **TinyAnalytics** is not the right tool for you. Please try [Google Analytics](https://analytics.google.com), [Open Web Analytics](https://www.openwebanalytics.com/) or [Piwik](https://www.piwik.org/) instead. I personally found the two last ones [not very handy for me](http://josephbasquin.fr/aboutanalytics).

> After years, I've noticed that **I prefer to have few (important) informations that I can consult each day in 30 seconds**, rather than lots of informations for which I would need 15 or 30 minutes per day for an in-depth analysis.

## Install

There are four easy steps. (I could have made an installer script that does everything out-of-the box, but doing it manually is a good way to see exactly what happens).

1) Unzip this package in a directory, e.g. `/var/www/TinyAnalytics/`.

2) Give the appropriate permissions:

    chmod 777 logs/
    chmod 755 summarize.py

3) Add the following tracking code to your websites at then end of `.php` files, e.g. `/var/www/mywebsite/index.php`:

    <?php 
    require '/var/www/TinyAnalytics/tracker.php';
    record_visit('mywebsite');
    ?>

4) Add this with `crontab -e`, to refresh analytics data every 1 hour:

    0 * * * * /var/www/TinyAnalytics/summarize.py

(Optional) Modify your password in line 2 of `index.php`. Default password is `abcdef`.    

It's done! Visit at least one of your tracked websites, run `./summarize.py` manually (this will be needed just once, then it will be done automatically every hour), and open `index.php` in your browser!

## Todo

* Filter bots with user agents

* (Maybe but only if same user experience) Replace Google Charts JavaScript code by another open-source chart-generating JavaScript library

* Some websites don't expose referer, [is there a way](https://stackoverflow.com/q/41466351/1422096) to solve this? 

## About

Author: Joseph Ernest ([@JosephErnest](https://twitter.com/JosephErnest))

Other projects: [BigPicture](http://bigpicture.bi), [bigpicture.js](https://github.com/josephernest/bigpicture.js), [AReallyBigPage](https://github.com/josephernest/AReallyBigPage), [SamplerBox](http://www.samplerbox.org), [Void](http://www.thisisvoid.org), [TalkTalkTalk](https://github.com/josephernest/TalkTalkTalk), [YellowNoiseAudio](http://www.yellownoiseaudio.com), [bloggggg](https://github.com/josephernest/bloggggg), etc.

Thanks to [WhiteHat](http://stackoverflow.com/users/5090771/whitehat) for his help on the chart visualization design.

## License

MIT license
