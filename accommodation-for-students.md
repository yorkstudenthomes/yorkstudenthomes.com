---
title: Accommodation for Students
---
## Accommodation for Students

[Accommodation for Students](https://www.accommodationforstudents.com) are the number 1 student accommodation search engine.

<!-- ** Start of _better_ Accommodation for Students Banner Ad ** -->
<div id="afs">
    <p><a href="https://www.accommodationforstudents.com"><img alt="Accommodation for Students" src="/images/afs-logo.png" /></a></p>

    <p>{% for link in site.data.afs.places -%}
    <a href="https://www.accommodationforstudents.com{{ link.url }}">{{ link.text }}</a>
    {% endfor %}</p>

    <p>{% for link in site.data.afs.extras -%}
    <a href="https://www.accommodationforstudents.com{{ link.url }}">{{ link.text }}</a>
    {% endfor %}</p>

    <p><strong><a href="https://www.accommodationforstudents.com"> The UK Wide Student Accommodation Search Engine</a></strong></p>
</div>
