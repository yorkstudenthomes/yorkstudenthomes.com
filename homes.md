---
title: Homes
---

## Homes

<ul>
  {%- assign sorted_homes = site.homes | sort: 'position' -%}
  {%- for home in sorted_homes %}
    <li><a href="{{ home.url }}">{{ home.name }}</a></li>
  {%- endfor %}
</ul>
