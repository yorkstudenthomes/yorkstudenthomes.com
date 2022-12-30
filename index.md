---
layout: default
---

## York Student Homes

If you are looking for a house to live in, with your friends, while you are studying at York University or York St John in York then you've just found a great place to find one!

We're a family business and have been renting houses in York for more than 20 years. We have houses for groups of 4, 5 and 6 in various areas of York to suit your needs!

## York House Share

We provide our superbly located properties for both student and professional living in and around the heart of York.

Please follow us at Instagram for more details: [@yorkhouseshare](https://www.instagram.com/yorkhouseshare/)

## Contact Us

All correspondence should be sent to the following address:

<address>
    Bec Nutton<br />
    50 Manor Heath<br />
    Copmanthorpe<br />
    York<br />
    YO23 3UP<br />
</address>

<dl>
{%- for contact in site.data.contact %}
  <dt>{{ contact.name }}</dt>

  {% for value in contact.value -%}
  {%- capture href -%}
    {%- if value contains '@' -%}
      mailto:{{ value }}
    {%- else -%}
      tel:+44{{ value | replace: ' ', '' | remove_first: '0' }}
    {%- endif -%}
  {%- endcapture -%}

  <dd><a href="{{ href }}">{{ value | replace: ' ', '&nbsp;' }}</a></dd>
  {% endfor -%}
{%- endfor -%}
</dl>
