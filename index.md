---
layout: default
---

{%- assign available = site.homes | where: 'rented', false | size -%}
{%- if available == 0 -%}
<div class="info">
    <h2>All houses unavailable</h2>
    <p>All of our houses have been taken for this year.</p>
</div>
{%- endif %}

## York Student Homes --- {{ site.data.config.year }}–{{ site.data.config.year | plus: 1 }} academic year

If you are looking for a house to live in, with your friends, while you are studying at York University or York St John in York then you've just found a great place to find one!

We're a family business and have been renting houses to university students in York for more than 20 years. We have houses for groups of 4, 5 and 6 in various areas of York to suit your needs!

Take a look at our web site and just give us a call, or email us, if you wish to view any or all of our properties!

We rent the houses by the **academic year** (beginning of July to end of June the following year). All of our houses are well-maintained and economical to run.

## Accommodation for Students

We are now listed on [Accommodation for Students](/accommodation-for-students/), the number 1 student accommodation search engine.
