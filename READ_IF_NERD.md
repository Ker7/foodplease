# FoodPlease - DevDiary
*Non-Comprehensive ramble of this AI-assisted development*

## 🎯 "I have a dream..."

## Origin
**FoodPlease** is a concatenated word that is also the cheatcode of attaining the food resource in the game of Age Of Empires II. 

### Core Philosophy
- **Write some log**
- **when do stuff**
- **In this way**
- **remember later**

---

23.07.2025
I vibed up the main project, had some bugs and set up Units and all views for:
- Ingredients/Inventory
- Recipes 
- Meal Plans

Created unit aggregation to calculate final Ingredient needs for the meal plan

24.07.2025
Fixed some bugs and aligned a bit the view, made ingredient Grouped Categorically

25.07.2025
Patched legacy unit addition. Tested with 10tbs on pasta into another recipe. Aggregation worked nice!

@todo's:
- [-]
- [?] add option to remove recipe from meal plan and allow only one meal per breakfast/lunch/dinner
- [-] add the "Clear button" to the top, after Breakfast/lunch title, and make the recipe title name wrap, as it is overflowing the container 
- [-] aggregate the meal plan data correctly, with the same recipes current it is not summating all the ingredients, just showing for one, also remove js dialog that asks about removing the recipe
- [-]
- [-]
- [-] reload the meal plan page or update the shopping list with the addition of retraction of recipes from the meal plan
- [-] write tests
- [-] seed proper data
- [-] get rid of migrations that patch the broken data
- [-] solve known bugs like viewFragment rendering unexpectedli
- [-] implement htmx features to affect other areas of the page that might need update
- [-]
- [-]