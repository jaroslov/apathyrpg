<?xml version="1.0" encoding="UTF-8" ?>

<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
  <xs:element name="and" type="xs:string" />

  <xs:element name="Apathy" type="xs:string" />

  <xs:element name="apathy-game">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="book" />
        <xs:element ref="raw-data" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="bns">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="bOff">
    <xs:complexType mixed="true">
      <xs:choice>
        <xs:element ref="plusminus" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="book">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="section" maxOccurs="unbounded" />
      </xs:sequence>
      <xs:attribute name="name" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="caption">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="category">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="default" minOccurs="0" />
        <xs:element ref="datum" minOccurs="0" maxOccurs="unbounded" />
      </xs:sequence>
      <xs:attribute name="name" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="cell">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="description-list" />
        <xs:element ref="equation" />
        <xs:element ref="example" />
        <xs:element ref="figure" />
        <xs:element ref="itemized-list" />
        <xs:element ref="note" />
        <xs:element ref="numbered-list" />
        <xs:element ref="reference" />
        <xs:element ref="section" />
        <xs:element ref="summarize" />
        <xs:element ref="table" />
        <xs:element ref="text" />
        <xs:element ref="title" />
      </xs:choice>
      <xs:attribute name="border" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="none" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
      <xs:attribute name="colfmt" type="xs:string" use="optional" />
      <xs:attribute name="span" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="11" />
            <xs:enumeration value="12" />
            <xs:enumeration value="2" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
    </xs:complexType>
  </xs:element>

  <xs:element name="datum">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="field" maxOccurs="unbounded" />
      </xs:sequence>
      <xs:attribute name="name" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="default">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="field" maxOccurs="unbounded" />
      </xs:sequence>
      <xs:attribute name="name" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="define">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="description">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="description-list">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="item" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="dollar" type="xs:string" />

  <xs:element name="equation">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="example">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="title" />
        <xs:element ref="text" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="face">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="field">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="description-list" />
        <xs:element ref="equation" />
        <xs:element ref="example" />
        <xs:element ref="figure" />
        <xs:element ref="itemized-list" />
        <xs:element ref="note" />
        <xs:element ref="numbered-list" />
        <xs:element ref="reference" />
        <xs:element ref="section" />
        <xs:element ref="summarize" />
        <xs:element ref="table" />
        <xs:element ref="text" />
        <xs:element ref="title" />
      </xs:choice>
      <xs:attribute name="qsummary" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="yes" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
      <xs:attribute name="name" type="xs:string" use="required" />
      <xs:attribute name="colfmt" type="xs:string" use="optional" />
      <xs:attribute name="table" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="yes" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
      <xs:attribute name="description" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="yes" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
      <xs:attribute name="title" use="optional">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="yes" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
    </xs:complexType>
  </xs:element>

  <xs:element name="figure">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="table" />
        <xs:element ref="caption" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="footnote">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="head">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="cell" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="item">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="description" />
        <xs:element ref="description-list" />
        <xs:element ref="equation" />
        <xs:element ref="example" />
        <xs:element ref="figure" />
        <xs:element ref="itemized-list" />
        <xs:element ref="note" />
        <xs:element ref="numbered-list" />
        <xs:element ref="reference" />
        <xs:element ref="section" />
        <xs:element ref="summarize" />
        <xs:element ref="table" />
        <xs:element ref="text" />
        <xs:element ref="title" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="itemized-list">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="item" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="kind">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="ldquo" type="xs:string" />

  <xs:element name="lsquo" type="xs:string" />

  <xs:element name="math">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="mfrac" />
        <xs:element ref="mrow" />
        <xs:element ref="msup" />
        <xs:element ref="times" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="mdash" type="xs:string" />

  <xs:element name="mfrac">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="mi" />
        <xs:element ref="mn" />
        <xs:element ref="mrow" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="mi">
    <xs:complexType mixed="true">
      <xs:choice>
        <xs:element ref="Sum" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="mn">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="mo">
    <xs:complexType mixed="true">
      <xs:choice>
        <xs:element ref="times" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="mrow">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="mfrac" />
        <xs:element ref="mi" />
        <xs:element ref="mn" />
        <xs:element ref="mo" />
        <xs:element ref="mrow" />
        <xs:element ref="msup" />
        <xs:element ref="munderover" />
        <xs:element ref="times" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="msup">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="mfrac" />
        <xs:element ref="mi" />
        <xs:element ref="mn" />
        <xs:element ref="mrow" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="mul">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="munderover">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="mrow" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="ndash" type="xs:string" />

  <xs:element name="notappl" type="xs:string" />

  <xs:element name="note">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="num">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="numbered-list">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="item" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="oslash" type="xs:string" />

  <xs:element name="ouml" type="xs:string" />

  <xs:element name="percent" type="xs:string" />

  <xs:element name="plusminus" type="xs:string" />

  <xs:element name="raw">
    <xs:complexType mixed="true" />
  </xs:element>

  <xs:element name="raw-data">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="category" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="rdquo" type="xs:string" />

  <xs:element name="reference">
    <xs:complexType>
      <xs:attribute name="hrid" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="rightarrow" type="xs:string" />

  <xs:element name="rOff">
    <xs:complexType mixed="true">
      <xs:choice>
        <xs:element ref="plusminus" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="roll">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="bOff" />
        <xs:element ref="bns" />
        <xs:element ref="face" />
        <xs:element ref="kind" />
        <xs:element ref="mul" />
        <xs:element ref="num" />
        <xs:element ref="rOff" />
        <xs:element ref="raw" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="row">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="cell" minOccurs="0" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="rsquo" type="xs:string" />

  <xs:element name="section">
    <xs:complexType>
      <xs:choice>
        <xs:element ref="description-list" />
        <xs:element ref="equation" />
        <xs:element ref="example" />
        <xs:element ref="figure" />
        <xs:element ref="itemized-list" />
        <xs:element ref="note" />
        <xs:element ref="numbered-list" />
        <xs:element ref="reference" />
        <xs:element ref="section" />
        <xs:element ref="summarize" />
        <xs:element ref="table" />
        <xs:element ref="text" />
        <xs:element ref="title" />
      </xs:choice>
      <xs:attribute name="kind" use="required">
        <xs:simpleType>
          <xs:restriction base="xs:NMTOKEN">
            <xs:enumeration value="chapter" />
            <xs:enumeration value="part" />
            <xs:enumeration value="section" />
          </xs:restriction>
        </xs:simpleType>
      </xs:attribute>
    </xs:complexType>
  </xs:element>

  <xs:element name="Sum" type="xs:string" />

  <xs:element name="summarize">
    <xs:complexType>
      <xs:attribute name="hrid" type="xs:string" use="required" />
    </xs:complexType>
  </xs:element>

  <xs:element name="table">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="head" />
        <xs:element ref="row" maxOccurs="unbounded" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="text">
    <xs:complexType mixed="true">
      <xs:choice>
        <xs:element ref="Apathy" />
        <xs:element ref="and" />
        <xs:element ref="define" />
        <xs:element ref="dollar" />
        <xs:element ref="footnote" />
        <xs:element ref="itemized-list" />
        <xs:element ref="ldquo" />
        <xs:element ref="lsquo" />
        <xs:element ref="math" />
        <xs:element ref="mdash" />
        <xs:element ref="ndash" />
        <xs:element ref="notappl" />
        <xs:element ref="oslash" />
        <xs:element ref="ouml" />
        <xs:element ref="percent" />
        <xs:element ref="rdquo" />
        <xs:element ref="rightarrow" />
        <xs:element ref="roll" />
        <xs:element ref="rsquo" />
        <xs:element ref="trademark" />
      </xs:choice>
    </xs:complexType>
  </xs:element>

  <xs:element name="times" type="xs:string" />

  <xs:element name="title">
    <xs:complexType>
      <xs:sequence>
        <xs:element ref="text" />
      </xs:sequence>
    </xs:complexType>
  </xs:element>

  <xs:element name="trademark" type="xs:string" />

</xs:schema>