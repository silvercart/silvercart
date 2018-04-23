# Defining CMS Fields

<div class="warning" markdown='1'>
**Stop:** This page is still under construction with several details not yet written.

It's published because some developers need parts of the information right now - use it on your own risk. 
</div>
We extended form scaffolding for our needs by adding some static methods. Before a CMS field definition loked something like this:
        
        pubic function getCMSFields() {
            $fields = parent::getCMSFields()
            return $fields;
        }

Now you shoud define them like this:

        pubic function getCMSFields() {
            $fields = DataObjectExtension::getCMSFields()
            return $fields;
        }

This has several advantages. If you DataObject is multilingual the multilingual fields get scaffolded correctly. Further you may define a method excludeFromScaffolding() on your DataObject which excludes fields performance friendly.

        public function excludeFromScaffolding() {
            $excludeFromScaffolding = array(
                'URLSegment'
            );

            $this->extend('updateExcludeFromScaffolding', $excludeFromScaffolding);

            return $excludeFromScaffolding;
        }

Add to this array field names or relation names of any kind of relation.
