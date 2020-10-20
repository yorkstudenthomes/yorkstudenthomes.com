---
title: Homes
---

## Homes

<ul>
  {%- for home in site.homes %}
    <li><a href="{{ home.url }}">{{ home.name }}</a></li>
  {%- endfor %}
</ul>
