# Создание таблица excel из текста типа:
# Название1:Значение|Название2:Значение...
import pandas as pd

# Ваш исходный текст
data = """Масса (кг):350|Толщина металла (мм):3.0|Объем (м³):0.5|Габариты (мм):2850х1000х830
Масса (кг):490|Толщина металла (мм):3.0|Объем (м³):1.0|Габариты (мм):3200х1250х1000
Высота (м):1.49 / 2.80 / 5.70|Масса (кг):14.5|Макс. нагрузка (кг):150"""

# Разделение каждой строки на свойства и значения
rows = [item.split('|') for item in data.split('\n')]

# Создание списка словарей, где каждый словарь представляет собой товар
products = []
for row in rows:
    product_dict = {}
    for prop_value in row:
        if ':' in prop_value:
            prop, value = prop_value.split(':', 1)
            product_dict[prop.strip()] = value.strip()
        else:
            product_dict[prop_value.strip()] = None
    products.append(product_dict)

# Создание DataFrame с помощью pandas
df = pd.DataFrame(products)

# Вывод DataFrame
print(df)

df.to_excel('output.xlsx')
