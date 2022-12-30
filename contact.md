---
title: Contact Us
---
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
