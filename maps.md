---
title: Maps
---

## Location Maps

<ul>
  {%- assign sorted_homes = site.homes | sort: 'position' -%}
  {%- for home in sorted_homes %}
    <li><a href="https://www.google.co.uk/maps/place/{{ home.name | append: ', York ' | append: home.postcode | replace: ' ','+' }}">{{ home.name }}</a></li>
  {%- endfor %}
</ul>

<p><a href="https://www.google.co.uk/maps/ms?f=q&amp;hl=en&amp;ie=UTF8&amp;msa=0&amp;msid=116566238079314889929.000442d0bdaad34e81b2f&amp;ll=53.956843,-1.075373&amp;spn=0.032776,0.080338&amp;z=14&amp;om=1">View all locations on one map</a></p>
