#include <iostream>
#include <map>
#include <string>
#include <boost/>

/*

  There is a subset of XML that I am interested in supporting:
  <tag attr="foo">
    <subtag attr="foo">
      TEXT
    </subtag>
  </tag>

  Which implies a straightforward datastructure.

*/

template <typename Kind, typename Content>
struct selector
{
  selector () : value () {}
  selector (Content const& ctnt)
    : value (ctnt) {}
  Content value;
};
template <typename Kind>
struct selector<Kind,void> {};
template <typename Kind, typename Content>
selector<Kind,Content> operator | (selector<Kind,Content>, Content const& ctnt)
{
  return selector<Kind,Content> (ctnt);
}
template <typename Kind>
selector<Kind,std::string> operator | (selector<Kind,std::string>,
				       const char* text)
{
  return selector<Kind,std::string> (std::string (text));
}

struct attribute_kind {};
struct child_kind {};
struct text_kind {};
struct name_kind {};
struct id_kind {};

typedef selector<attribute_kind,std::string> attribute;
typedef selector<child_kind,std::size_t> child;
typedef selector<text_kind,void> text;
typedef selector<name_kind,void> name;
typedef selector<id_kind,void> identifier;

static const attribute at = attribute ();
static const child ch = child ();
static const text t = text ();
static const name n = name ();
static const identifier id = identifier ();

class node
{
private:
  static std::string SDUMP;
  static node NDUMP;
protected:
  typedef std::map<std::string,std::string> attributes_type;
  typedef attributes_type::iterator at_iter;
  typedef attributes_type::const_iterator c_at_iter;
  typedef std::map<std::size_t,node> children_type;
  typedef children_type::iterator ch_iter;
  typedef children_type::const_iterator c_ch_iter;
  attributes_type attributes;
  children_type children;
  std::string text, name;
public:
  node () {}
  node (std::string const& nm)
    : name(nm) {}
  node (std::string const& nm, std::string const& txt)
    : name(nm), text(txt) {}
  virtual ~ node ()
  {
    this->clear ();
  }
  node (node const& N)
  {
    this->copy (N);
  }
  node& operator = (node const& N)
  {
    this->copy (N);
    return *this;
  }
  void clear ()
  {
    this->attributes.clear ();
    this->children.clear ();
    this->text = "";
    this->name = "";
  }
  void copy (node const& N)
  {
    this->attributes = N.attributes;
    this->children = N.children;
    this->text = N.text;
    this->name = N.name;
  }
  std::string const& operator [] (attribute const& attr) const
  {
    c_at_iter end = this->attributes.end ();
    c_at_iter itr = this->attributes.find (attr.value);
    if (end == itr)
      return SDUMP;
    return itr->second;
  }
  std::string& operator [] (attribute const& attr)
  {
    return this->attributes[attr.value];
  }
  node const& operator [] (child const& chld) const
  {
    c_ch_iter end = this->children.end ();
    c_ch_iter itr = this->children.find (chld.value);
    if (end == itr)
      return NDUMP;
    return itr->second;
  }
  node& operator [] (child const& chld)
  {
    return this->children[chld.value];
  }
  std::string const& operator [] (::text) const
  {
    return this->text;
  }
  std::string& operator [] (::text)
  {
    return this->text;
  }
  std::string const& operator [] (::name) const
  {
    return this->name;
  }
  std::string& operator [] (::name)
  {
    return this->name;
  }
  std::string const& operator [] (::identifier) const
  {
    return (*this)[at|"id"];
  }
  std::string& operator [] (::identifier)
  {
    return (*this)[at|"id"];
  }
  friend void write (std::ostream& o, node const& N,
                     std::string const& indent="")
  {
    // start-tag
    o << indent << "<" << N.name;
    c_at_iter ater = N.attributes.begin ();
    c_at_iter aend = N.attributes.end ();
    while (ater != aend)
      {
        o << " " << ater->first << "=\"" << ater->second << "\"";
        ++ater;
      }

    // contents
    if (N.text.empty () && N.children.empty ())
      o << " />";
    else
      {
        o << ">" << "\n" << indent+"  " << N.text << "\n";
        c_ch_iter cter = N.children.begin ();
        c_ch_iter cend = N.children.end ();
        while (cter != cend)
	        {
	          write (o, cter->second, indent+"  ");
	          o << "\n";
	          ++cter;
	        }
	      o << indent << "<" << N.name << ">";
      }
  }
  friend void read (std::istream& i, node& N)
  {
    // we're going to eat the whole damn file in one gulp
    // that's why it's called "XMLForStupid"
    std::string line;
    std::string lines;
    while (std::getline (i, line))
      lines += line;
    // 
  }	
  friend std::ostream& operator << (std::ostream& o, node const& N)
  {
    write (o, N);
    return o;
  }
  friend std::istream& operator >> (std::istream& i, node& N)
  {
    read (i, N);
    return i;
  }	
};

node node::NDUMP;
std::string node::SDUMP;

int main (int argc, char *argv[])
{
  // broken
  // children, attributes must return proxy-types
  // so that their respective access-tables get rebuilt

  node Doc ("baz"), Sub ("sub");
  Doc[at|"foo"] = "bar";
  Doc[t] = "Borsephus!";
  Doc[0] = Sub;

  std::cout << Doc << std::endl;

  return 0;
}
