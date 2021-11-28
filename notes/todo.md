# ToDos:

## Pending
- BUG: If a reference to an existing source wich has a reference as _first_ in the document inserts
itself before (i.e. a new _first_ reference of the same source) it don't render it's n (data-n)
- Create list of references
- Check that all the saving options work by diferent usage path, to look for bugs.

- Create Image Insertion With Indexed Captions.
- Personalize formats and CSS

- Creators Input
    - Throttle the 'creatorInput' call in logos editor

### Bugs


## Ongoin


## Done
- Check that saves an already created source ok.
    - Saves when attributes changed
    - Saves when creator attributes changed
    - Saves when creator Role changed
    - Saves when creator relevance changed
    - Saves when creator removed

- Check that saves a new source OK
    - Saves attributes
    - Saves participations
        - Saves new participations
        - Saves modified participations
        - Saves modified creator attributes participations

- FIXED: - [8/11/21] Participations don't change when changing source for editing.
    - WHEN: select a source for edit; go back to list; select another source for edit
    - RESULT: the first participations still the same
    - EXPECTED: the participations should match the new selected source
